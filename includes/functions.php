<?php
if ( !defined( 'ABSPATH' ) ) exit;

// Get plugin setting by key
function payje_wc_get_setting( $key, $default = null ) {

    $settings = get_option( 'woocommerce_payje_settings' );

    if ( isset( $settings[ $key ] ) && !empty( $settings[ $key ] ) ) {
        return $settings[ $key ];
    }

    return $default;

}

// Display notice
function payje_wc_notice( $message, $type = 'success' ) {

    $plugin = esc_html__( 'Payje for WooCommerce', 'payje-wc' );

    printf( '<div class="notice notice-%1$s"><p><strong>%2$s:</strong> %3$s</p></div>', esc_attr( $type ), $plugin, $message );

}

// Log a message in WooCommerce logs
function payje_wc_logger( $message ) {

    do_action( 'logger', $message );

    // if ( !function_exists( 'wc_get_logger' ) ) {
    //     return false;
    // }

    // return wc_get_logger()->add( 'payje-wc', $message );

}

// Get approved businesses from Payje
function payje_wc_get_businesses() {

    try {

        $payje = new Payje_WC_API();
        $payje->set_access_token( payje_get_access_token() );

        list( $code, $response ) = $payje->get_approved_businesses();

        $data = isset( $response['data'] ) ? $response['data'] : false;

        $businesses = array();

        if ( is_array( $data ) ) {

            foreach ( $data as $item ) {

                $business_id = isset( $item['id'] ) ? sanitize_text_field( $item['id'] ) : null;

                if ( !$business_id ) {
                    continue;
                }

                $businesses[ $business_id ] = array(
                    'id'             => $business_id,
                    'name'           => isset( $item['name'] ) ? sanitize_text_field( $item['name'] ) : null,
                    'integration_id' => isset( $item['integration']['id'] ) ? sanitize_text_field( $item['integration']['id'] ) : null,
                    'api_key'        => isset( $item['integration']['api_key'] ) ? sanitize_text_field( $item['integration']['api_key'] ) : null,
                    'signature_key'  => isset( $item['integration']['signature_key'] ) ? sanitize_text_field( $item['integration']['signature_key'] ) : null,
                );
            }
        }

        return $businesses;

    } catch ( Exception $e ) {
        return false;
    }

}

// Get business information from Payje by its ID
function payje_wc_get_business( $business_id ) {
    $businesses = payje_wc_get_businesses();
    return isset( $businesses[ $business_id ] ) ? $businesses[ $business_id ] : false;
}
