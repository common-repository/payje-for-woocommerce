<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Payje_WC {

    // Load dependencies
    public function __construct() {

        // Libraries
        require_once( PAYJE_WC_PATH . 'libraries/payje/class-payje.php' );

        // Functions
        require_once( PAYJE_WC_PATH . 'includes/functions.php' );

        // Admin
        require_once( PAYJE_WC_PATH . 'admin/class-payje-wc-admin.php' );

        // API
        require_once( PAYJE_WC_PATH . 'includes/class-payje-wc-api.php' );
    
        // Initialize payment gateway
        require_once( PAYJE_WC_PATH . 'includes/class-payje-wc-init.php' );
        
        if ( payje_is_logged_in() && payje_is_plugin_activated( 'woocommerce/woocommerce.php' ) ) {

            // Settings
            require_once( PAYJE_WC_PATH . 'admin/class-payje-wc-settings.php' );

        }

    }

}
new Payje_WC();
