<?php
if ( !defined( 'ABSPATH' ) ) exit;

// Get access token
function payje_get_access_token() {
    return get_transient( 'payje_access_token' );
}

// Update access token
function payje_update_access_token( $access_token, $remember = false ) {

    $expires = DAY_IN_SECONDS;

    if ( $remember ) {
        $expires = DAY_IN_SECONDS * 7;
    }

    return set_transient( 'payje_access_token', $access_token, 30 );

}

// Delete access token
function payje_delete_access_token() {
    return delete_transient( 'payje_access_token' );
}

// Check if the user is logged into Payje
function payje_is_logged_in() {
    return payje_get_access_token() ? true : false;
}

// Check if the specified plugin is installed and activated
function payje_is_plugin_activated( $plugin_path ) {
    return in_array( $plugin_path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
}

// Check if the specified plugin is installed
function payje_is_plugin_installed( $plugin_path ) {
    $installed_plugins = get_plugins();
    return array_key_exists( $plugin_path, $installed_plugins );
}
