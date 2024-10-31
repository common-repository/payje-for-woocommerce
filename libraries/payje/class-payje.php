<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'Payje' ) ) {
    define( 'PAYJE_FILE', __FILE__ );
    define( 'PAYJE_URL', plugin_dir_url( PAYJE_FILE ) );
    define( 'PAYJE_PATH', plugin_dir_path( PAYJE_FILE ) );
    define( 'PAYJE_VERSION', '1.0.0' );

    class Payje {

        // Load dependencies
        public function __construct() {

            // Autoload
            require_once( PAYJE_PATH . 'vendor/autoload.php' );

            // Functions
            require_once( PAYJE_PATH . 'includes/functions.php' );

            // API
            require_once( PAYJE_PATH . 'includes/abstracts/abstract-payje-client.php' );
            require_once( PAYJE_PATH . 'includes/class-payje-api.php' );

            // Admin
            require_once( PAYJE_PATH . 'admin/class-payje-admin.php' );

        }

    }
    new Payje();
}
