<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Payje_WC_Init {

    private $gateway_class = 'Payje_WC_Gateway';

    // Register hooks
    public function __construct() {

        add_action( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );
        add_action( 'init', array( $this, 'load_dependencies' ) );

    }

    // Register Payje as WooCommerce payment method
    public function register_gateway( $methods ) {

        global $current_screen;

        $current_screen_id = isset( $current_screen->id ) ? $current_screen->id : false;

        // This is to hide the payment method in the WooCommerce settings page
        if ( !is_admin() && $current_screen_id !== 'woocommerce_page_wc-settings' ) {
            $methods[] = $this->gateway_class;
        }

        return $methods;

    }

    // Load required files
    public function load_dependencies() {

        if ( !class_exists( 'WC_Payment_Gateway' ) ) {
            return;
        }

        require_once( PAYJE_WC_PATH . 'includes/class-payje-wc-gateway.php' );

    }

}
new Payje_WC_Init();
