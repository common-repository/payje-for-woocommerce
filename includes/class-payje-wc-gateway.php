<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Payje_WC_Gateway extends WC_Payment_Gateway {

    private $payje;

    private $api_key;
    private $signature_key;
    private $business_id;
    private $environment;
    private $debug;

    public function __construct() {

        $this->id                 = 'payje';
        $this->method_title       = __( 'Payje', 'payje-wc' );
        $this->method_description = __( 'Enable Payje payment gateway for your site.', 'payje-wc' );
        $this->order_button_text  = __( 'Pay with Payje', 'payje-wc' );
        $this->supports           = array( 'products' );

        $this->title              = $this->get_option( 'title' ) . __( ' Payje', 'payje-wc' );
        $this->description        = $this->get_option( 'description' );
        $this->icon               = PAYJE_URL . 'assets/images/logo-payje.svg';

        $this->api_key            = $this->get_option( 'api_key' );
        $this->signature_key      = $this->get_option( 'signature_key' );
        $this->business_id        = $this->get_option( 'business_id' );
        $this->environment        = $this->get_option( 'environment' );
        $this->debug              = $this->get_option( 'debug' ) === 'yes' ? true : false;

        $this->register_hooks();

        // Check if the payment gateway is ready to use
        if ( !$this->validate_required_settings() ) {
            $this->enabled = 'no';
        }

        $this->init_api();

    }

    // Register WooCommerce payment gateway hooks
    private function register_hooks() {

        add_action( 'woocommerce_api_' . $this->id . '_wc_gateway', array( $this, 'handle_ipn' ) );
        add_action( 'woocommerce_thankyou', array( $this, 'add_thank_you_page_transaction_id' ));

    }

    function add_thank_you_page_transaction_id( $order_id ) {

        if(isset($_GET['payment_id'])){

            echo "<script> \n";
            echo "var ul = document.getElementsByClassName('woocommerce-thankyou-order-details') \n";
            echo "var li = document.createElement('li') \n";
            echo "var label = document.createTextNode('Transaction ID:') \n";
            echo "var value = document.createElement('strong') \n";
            echo "value.innerHTML = " . sanitize_text_field( $_GET['payment_id'] ) .  "\n";
            echo "li.append(label) \n";
            echo "li.append(value) \n";
            echo "ul[0].appendChild(li) \n";
            echo "</script> \n";

        }
   
   }

    // Check if all required settings is filled
    private function validate_required_settings() {
        return $this->api_key
            && $this->signature_key
            && $this->business_id;
    }

    // Initialize API
    private function init_api() {
        $this->payje = new Payje_WC_API();
    }

    // Process the payment
    public function process_payment( $order_id ) {

        if ( !$this->validate_required_settings() ) {
            return false;
        }

        if ( !$order = wc_get_order( $order_id ) ) {
            return false;
        }

        try {
            // Redirect to the payment page if the transaction ID has been saved
            if ( $transaction_id = get_post_meta( $order_id, '_transaction_id', true ) ) {

                if ( $payment_url = $this->get_payment_link( $transaction_id ) ) {
                    return array(
                        'result'   => 'success',
                        'redirect' => $payment_url,
                    );
                }
            }

            payje_wc_logger( 'Creating payment for order #' . $order_id );

            $params = array(
                'email'        => $order->get_billing_email(),
                'currency'     => $order->get_currency(),
                'amount'       => number_format($order->get_total(), 2),
                'title'        => get_bloginfo(),
                'phone_no'     => preg_replace('/[^0-9]/', '', $order->get_billing_phone() ),
                'description'  => sprintf( __( 'Payment for Order #%d', 'payje-wc' ), $order_id ),
                'redirect_url' => $this->get_return_url( $order ),
                'reference'    => $order->get_id(),
                'reference_2'  => 'woocommerce',
                'send_email'   => true,
            );

            list( $code, $response ) = $this->payje->create_payment_link( $params );

            $errors = isset( $response['errors'] ) ? $response['errors'] : false;

            if ( $errors ) {
                foreach ( $errors as $error ) {
                    throw new Exception( $error[0] );
                }
            }

            payje_wc_logger( 'Payment created for order #' . $order_id );

            if ( isset( $response['data']['_id'] ) ) {
                update_post_meta( $order->get_id(), '_transaction_id', wc_clean( $response['data']['_id'] ) );
            }

            // Redirect to the payment page
            if ( isset( $response['data']['payment_url'] ) ) {
                return array(
                    'result'   => 'success',
                    'redirect' => $response['data']['payment_url'],
                );
            }

        } catch ( Exception $e ) {
            wc_add_notice( __( 'Payment error: ', 'payje-wc' ) . $e->getMessage(), 'error' );
        }

        return;

    }

    // Get payment link based on bill ID saved in the WooCommerce
    private function get_payment_link( $transaction_id ) {

        try {

            list( $code, $response ) = $this->payje->get_payment_link( $transaction_id );

            $errors = isset( $response['errors'] ) ? $response['errors'] : false;

            if ( $errors ) {
                foreach ( $errors as $error ) {
                    throw new Exception( $error[0] );
                }
            }

            if ( isset( $response['data']['payment_url'] ) ) {
                return $response['data']['payment_url'];
            }

        } catch ( Exception $e ) {}

        return false;

    }

    // Handle IPN
    public function handle_ipn() {

        $response = $this->payje->get_ipn_response();

        if ( !$response ) {
            payje_wc_logger( 'IPN webhook failed' );
            wp_die( 'Payje IPN webhook failed', 'Payje IPN', array( 'response' => 200 ) );
        }

        payje_wc_logger( 'IPN webhook response: ' . wp_json_encode( $response ) );

        if ( $response['payment_link_reference_2'] !== 'woocommerce' ) {
            return false;
        }

        $order_id = absint( $response['payment_link_reference'] );
        $order = wc_get_order( $order_id );

        if ( !$order ) {
            payje_wc_logger( 'Order #' . $order_id . ' not found' );
            return false;
        }

        // Check if it is correct payment link ID
        if ( get_post_meta( $order_id, '_transaction_id', true ) !== $response['payment_link_id'] ) {
            return false;
        }

        // Check if the payment already marked as paid
        if ( get_post_meta( $order_id, $response['payment_link_id'], true ) === 'paid' ) {
            return false;
        }

        try {
            payje_wc_logger( 'Verifying hash for order #' . $order_id );
            $this->payje->validate_ipn_response( $response );
        } catch ( Exception $e ) {
            payje_wc_logger( $e->getMessage() );
            wp_die( $e->getMessage(), 'Payje IPN', array( 'response' => 200 ) );
        } finally {
            payje_wc_logger( 'Verified hash for order #' . $order_id );
        }

        if ( $response['payment_status'] == 1 ) {
        }

        switch ( $response['payment_status'] ) {
            case 1:
                $this->handle_success_payment( $order, $response );
                break;

            case 2:
                $this->handle_pending_payment( $order, $response );
                break;

            default:
                $this->handle_failed_payment( $order, $response );
                break;
        }

        payje_wc_logger( 'IPN webhook success' );
        wp_die( 'Payje IPN webhook success', 'Payje IPN', array( 'response' => 200 ) );

    }

    // Handle success payment
    private function handle_success_payment( WC_Order $order, $response ) {

        update_post_meta( $order->get_id(), '_transaction_id', $response['payment_link_id'] );
        update_post_meta( $order->get_id(), $response['payment_link_id'], 'paid' );

        $order->payment_complete();

        $is_sandbox = payje_wc_get_setting( 'environment' ) == 'sandbox';
        $sandbox_label = $is_sandbox ? __( 'Yes', 'payje-wc' ) : __( 'No', 'payje-wc' );

        $reference = '<br>.<br>' . esc_html__( 'Payment ID: ', 'payje-wc' ) . $response['payment_link_id'];
        $reference .= '<br>' . esc_html__( 'Sandbox: ', 'payje-wc' ) . $sandbox_label;

        if ( $response['payment_message'] ) {
            $reference .= '<br>.<br>' . esc_html__( 'Other information: ', 'payje-wc' ) . $response['payment_message'];
        }

        $order->add_order_note( esc_html__( 'Payment success!', 'payje-wc' ) . $reference );

        payje_wc_logger( 'Order #' . $order->get_id() . ' has been marked as Paid' );

    }

    // Handle pending payment
    private function handle_pending_payment( WC_Order $order, $response ) {

        update_post_meta( $order->get_id(), '_transaction_id', $response['payment_link_id'] );
        update_post_meta( $order->get_id(), $response['payment_link_id'], 'pending' );

        $order->update_status( 'wc-pending' );

        $is_sandbox = payje_wc_get_setting( 'environment' ) == 'sandbox';
        $sandbox_label = $is_sandbox ? __( 'Yes', 'payje-wc' ) : __( 'No', 'payje-wc' );

        $reference = '<br>.<br>' . esc_html__( 'Payment ID: ', 'payje-wc' ) . $response['payment_link_id'];
        $reference .= '<br>' . esc_html__( 'Sandbox: ', 'payje-wc' ) . $sandbox_label;

        if ( $response['payment_message'] ) {
            $reference .= '<br>.<br>' . esc_html__( 'Other information: ', 'payje-wc' ) . $response['payment_message'];
        }

        $order->add_order_note( esc_html__( 'Payment pending!', 'payje-wc' ) . $reference );

        payje_wc_logger( 'Order #' . $order->get_id() . ' has been marked as Pending Payment' );

    }

    // Handle failed payment
    private function handle_failed_payment( WC_Order $order, $response ) {

        update_post_meta( $order->get_id(), '_transaction_id', $response['payment_link_id'] );
        update_post_meta( $order->get_id(), $response['payment_link_id'], 'failed' );

        $order->update_status( 'wc-failed' );

        $is_sandbox = payje_wc_get_setting( 'environment' ) == 'sandbox';
        $sandbox_label = $is_sandbox ? __( 'Yes', 'payje-wc' ) : __( 'No', 'payje-wc' );

        $reference = '<br>.<br>' . esc_html__( 'Payment ID: ', 'payje-wc' ) . $response['payment_link_id'];
        $reference .= '<br>' . esc_html__( 'Sandbox: ', 'payje-wc' ) . $sandbox_label;

        if ( $response['payment_message'] ) {
            $reference .= '<br>.<br>' . esc_html__( 'Other information: ', 'payje-wc' ) . $response['payment_message'];
        }

        $order->add_order_note( esc_html__( 'Payment failed!', 'payje-wc' ) . $reference );

        payje_wc_logger( 'Order #' . $order->get_id() . ' has been marked as Failed' );

    }

}
