<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Payje_WC_API extends Payje_API {

    // Initialize API
    public function __construct() {

        $this->set_api_key( payje_wc_get_setting( 'api_key' ) );
        $this->set_signature_key(payje_wc_get_setting( 'signature_key' ) );
        $this->set_business_id(payje_wc_get_setting( 'business_id' ) );
        $this->set_environment( payje_wc_get_setting( 'environment', 'sandbox' ) );
        $this->set_debug( payje_wc_get_setting( 'debug' ) ? true : false );

    }

    // Log a message in WooCommerce logs
    protected function log( $message ) {

        if ( $this->debug ) {
            payje_wc_logger( $message );
        }

    }

}
