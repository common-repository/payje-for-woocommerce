<?php
/**
 * Plugin Name:       Payje for WooCommerce
 * Description:       Payje payment integration for WooCommerce.
 * Version:           1.0.1
 * Requires at least: 4.6
 * Requires PHP:      7.0
 * Author:            Edaran IT Service.
 * Author URI:        https://www.edaran.com/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( class_exists( 'Payje_WC' ) ) return;

define( 'PAYJE_WC_FILE', __FILE__ );
define( 'PAYJE_WC_URL', plugin_dir_url( PAYJE_WC_FILE ) );
define( 'PAYJE_WC_PATH', plugin_dir_path( PAYJE_WC_FILE ) );
define( 'PAYJE_WC_BASENAME', plugin_basename( PAYJE_WC_FILE ) );
define( 'PAYJE_WC_VERSION', '1.0.0' );

// Plugin core class
require( PAYJE_WC_PATH . 'includes/class-payje-wc.php' );
