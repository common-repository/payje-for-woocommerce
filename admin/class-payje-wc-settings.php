<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Payje_WC_Settings {

    private $id = 'payje_wc_settings';

    private $keys = array(
        'enabled',
        'title',
        'description',
        'api_key',
        'signature_key',
        'environment',
        'business_id',
    );

    // Register hooks
    public function __construct() {

        add_action( 'admin_menu', array( $this, 'register_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'wp_ajax_payje_wc_update_settings', array( $this, 'update_settings' ) );
        add_action( 'wp_ajax_payje_wc_retrieve_api_credentials', array( $this, 'retrieve_api_credentials' ) );
        add_action( 'wp_ajax_payje_wc_set_webhook', array( $this, 'set_webhook' ) );

        $this->init();

    }

    // Initialize settings
    private function init() {

        $settings = get_option( 'woocommerce_payje_settings' );

        $defaults = array(
            'enabled'       => 'no',
            'title'         => __( 'Pay Using', 'payje-wc' ),
            'description'   => __( 'Pay with Maybank2u, CIMB Clicks, Bank Islam, RHB, Hong Leong Bank, Bank Muamalat, Public Bank, Alliance Bank, Affin Bank, AmBank, Bank Rakyat, UOB, Standard Chartered, Boost, e-Wallet.' ),
            'api_key'       => '',
            'signature_key' => '',
            'environment'   => 'sandbox',
            'business_id'   => '',
        );

        if ( !$settings ) {
            update_option( 'woocommerce_payje_settings', $defaults );
        }

    }

    // Register admin menu
    public function register_menu() {

        add_submenu_page(
            'payje',
            __( 'Payje – WooCommerce Settings', 'payje-wc' ),
            __( 'WooCommerce', 'payje-wc' ),
            'manage_options',
            $this->id,
            array( $this, 'view_page' )
        );

    }

    // Get the views of the settings page
    public function view_page() {

        $enabled       = payje_wc_get_setting( 'enabled' );
        $title         = payje_wc_get_setting( 'title' );
        $description   = payje_wc_get_setting( 'description' );
        $api_key       = payje_wc_get_setting( 'api_key' );
        $signature_key = payje_wc_get_setting( 'signature_key' );
        $environment   = payje_wc_get_setting( 'environment' );
        $business_id   = payje_wc_get_setting( 'business_id' );

        $businesses = payje_wc_get_businesses();
        $current_business = null;

        // Get current business
        if ( $business_id && $businesses ) {
            foreach ( $businesses as $item ) {
                if ( $item['id'] == $business_id ) {
                    $current_business = $item;
                    break;
                }
            }
        }

        ob_start();
        require_once( PAYJE_WC_PATH . 'admin/views/settings.php' );

        echo ob_get_clean();

    }

    // Enqueue styles & scripts
    public function enqueue_scripts( $hook ) {

        if ( $hook !== 'payje_page_payje_wc_settings' ) {
            return;
        }

        wp_enqueue_style( 'payje-wc-admin', PAYJE_WC_URL . 'assets/css/admin.css', array(), PAYJE_WC_VERSION, 'all' );
        wp_enqueue_script( 'payje-wc-admin', PAYJE_WC_URL . 'assets/js/admin.js', array( 'jquery', 'jquery-validate', 'sweetalert2' ), PAYJE_WC_VERSION, true );

        wp_localize_script( 'payje-admin', 'payje_wc_update_settings', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'payje_wc_update_settings_nonce' ),
        ) );

        wp_localize_script( 'payje-admin', 'payje_wc_retrieve_api_credentials', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'payje_wc_retrieve_api_credentials_nonce' ),
        ) );

        wp_localize_script( 'payje-admin', 'payje_wc_set_webhook', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'payje_wc_set_webhook_nonce' ),
        ) );

    }

    // Update WooCommerce settings
    public function update_settings() {

        check_ajax_referer( 'payje_wc_update_settings_nonce', 'nonce' );

        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;

        if ( !wp_verify_nonce( $nonce, 'payje_wc_update_settings_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid nonce', 'payje' ),
            ), 400 );
        }

        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'No permission to update the settings', 'payje' ),
            ), 400 );
        }

        $settings = get_option( 'woocommerce_payje_settings' );

        // Go through each settings key, then check if there have POST request.
        // If have, update the settings value.
        foreach ( $this->keys as $key ) {
            if ( $key == 'description' ) {
                $value = isset( $_POST[ $key ] ) ? sanitize_textarea_field( $_POST[ $key ] ) : null;
            } else {
                $value = isset( $_POST[ $key ] ) ? sanitize_text_field( $_POST[ $key ] ) : null;
            }

            if ( $value ) {
                $settings[ $key ] = $value;
            }
        }

        update_option( 'woocommerce_payje_settings', $settings );

        wp_send_json_success();

    }

    // Retrieve API credentials from Payje
    public function retrieve_api_credentials() {

        check_ajax_referer( 'payje_wc_retrieve_api_credentials_nonce', 'nonce' );

        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;

        if ( !wp_verify_nonce( $nonce, 'payje_wc_retrieve_api_credentials_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid nonce', 'payje' ),
            ), 400 );
        }

        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'No permission to update the settings', 'payje' ),
            ), 400 );
        }

        $business_id = payje_wc_get_setting( 'business_id' );

        if ( !$business_id ) {
            wp_send_json_error( array(
                'message' => __( 'No business selected', 'payje' ),
            ), 400 );
        }

        $business = payje_wc_get_business( $business_id );

        if ( !$business ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid business', 'payje' ),
            ), 400 );
        }

        // Update API credentials into the database ////////////////////////////

        $settings = get_option( 'woocommerce_payje_settings' );

        if ( isset( $business['integration_id'] ) ) {
            $settings['integration_id'] = $business['integration_id'];
        }

        if ( isset( $business['api_key'] ) ) {
            $settings['api_key'] = $business['api_key'];
        }

        if ( isset( $business['signature_key'] ) ) {
            $settings['signature_key'] = $business['signature_key'];
        }

        update_option( 'woocommerce_payje_settings', $settings );

        ////////////////////////////////////////////////////////////////////////

        wp_send_json_success( $business );

    }

    // Set WooCommerce webhook URL in Payje
    public function set_webhook() {

        check_ajax_referer( 'payje_wc_set_webhook_nonce', 'nonce' );

        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;

        if ( !wp_verify_nonce( $nonce, 'payje_wc_set_webhook_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid nonce', 'payje' ),
            ), 400 );
        }

        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'No permission to update the settings', 'payje' ),
            ), 400 );
        }

        $business_id = payje_wc_get_setting( 'business_id' );
        $integration_id = payje_wc_get_setting( 'integration_id' );

        if ( !$business_id ) {
            wp_send_json_error( array(
                'message' => __( 'No business selected', 'payje' ),
            ), 400 );
        }

        if ( !$integration_id ) {
            wp_send_json_error( array(
                'message' => __( 'Missing integration ID for selected business', 'payje' ),
            ), 400 );
        }

        try {

            $payje = new Payje_WC_API();
            $payje->set_access_token( payje_get_access_token() );

            // Get all webhooks because we need to delete existing webhook first
            // 1 = payment.created
            list( $code, $response ) = $payje->get_webhooks( $business_id, $integration_id );

            $webhooks = isset( $response['data']['data'] ) ? $response['data']['data'] : array();

            if ( $webhooks ) {
                foreach ( $webhooks as $webhook ) {
                    if ( !isset( $webhook['_id'] ) ) {
                        continue;
                    }

                    // Delete existing webhook first
                    $payje->delete_webhook( $business_id, $integration_id, $webhook['_id'], array( 'enabled' => true ) );
                }
            }

            $params = array(
                'name'    => 'payment.created',
                'url'     => WC()->api_request_url( 'payje_wc_gateway' ),
                'enabled' => true,
            );

            list( $code, $response ) = $payje->store_webhook( $business_id, $integration_id, $params );

            $errors = isset( $response['errors'] ) ? $response['errors'] : false;

            if ( $errors ) {
                foreach ( $errors as $error ) {
                    throw new Exception( $error[0] );
                }
            }

        } catch ( Exception $e ) {
            wp_send_json_error( array(
                'message' => $e->getMessage(),
            ), 400 );
        }

        wp_send_json_success( $business_id );

    }

}
new Payje_WC_Settings();
