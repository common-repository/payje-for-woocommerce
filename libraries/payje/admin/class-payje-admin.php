<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Payje_Admin {

    private $id = 'payje';

    // Register hooks
    public function __construct() {

        add_action( 'admin_menu', array( $this, 'register_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'wp_ajax_payje_login', array( $this, 'login' ) );
        add_action( 'wp_ajax_payje_logout', array( $this, 'logout' ) );

    }

    // Register admin menu
    public function register_menu() {

        add_menu_page(
            __( 'Payje', 'payje' ),
            __( 'Payje', 'payje' ),
            'manage_options',
            $this->id,
            array( $this, 'view_page' ),
            PAYJE_URL . 'assets/images/logo-payje.svg',
            65
        );

        if ( payje_is_logged_in() ) {
            add_submenu_page(
                'payje',
                __( 'Payje – Dashboard', 'payje' ),
                __( 'Dashboard', 'payje' ),
                'manage_options',
                $this->id
            );
        }

    }

    // Get the views of the admin page based on user authentication
    public function view_page() {

        if ( !payje_is_logged_in() ) {
            echo $this->login_page();
        } else {
            echo $this->dashboard_page();
        }

    }

    // Get the views of the login page
    private function login_page() {

        ob_start();
        require_once( PAYJE_PATH . 'admin/views/login.php' );

        return ob_get_clean();

    }

    // Get the views of the dashboard page
    private function dashboard_page() {

        $payje_wc_plugin = $this->get_plugin_url( 'woocommerce/woocommerce.php', 'payje-for-woocommerce/payje-wc.php', 'wc', __( 'WooCommerce', 'payje' ) );
        $payje_gf_plugin = $this->get_plugin_url( 'gravityforms/gravityforms.php', 'payje-for-woocommerce/payje-gf.php', 'gf', __( 'Gravity Forms', 'payje' ) );

        ob_start();
        require_once( PAYJE_PATH . 'admin/views/dashboard.php' );

        return ob_get_clean();

    }

    // Generate plugin URL based on installed and activated plugins
    private function get_plugin_url( $main_plugin_file, $payje_plugin_file, $settings_page_slug, $main_plugin_name ) {

        $is_main_plugin_activated = payje_is_plugin_activated( $main_plugin_file );
        $is_payje_plugin_activated = payje_is_plugin_activated( $payje_plugin_file );

        $is_main_plugin_installed = payje_is_plugin_installed( $main_plugin_file );
        $is_payje_plugin_installed = payje_is_plugin_installed( $payje_plugin_file );
        
        $payje_plugin_download_url = "";

        // If main plugin and Payje plugin is activated, return settings page URL
        if ( $is_main_plugin_activated && $is_payje_plugin_activated ) {
            return array(
                'label' => __( 'Configure', 'payje' ),
                'url'   => admin_url( 'admin.php?page=payje_' . $settings_page_slug . '_settings' )
            );
        }

        // If Payje plugin is installed but not activated, return plugin activation URL
        if ( $is_payje_plugin_installed && !$is_payje_plugin_activated ) {
            return array(
                'label' => __( 'Activate', 'payje' ),
                'url'   => wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $payje_plugin_file ), 'activate-plugin_' . $payje_plugin_file )
            );
        }

        // If Payje plugin is not installed, return plugin download URL
        if ( !$is_payje_plugin_installed ) {
            return array(
                'label' => __( 'Download', 'payje' ),
                'url'   => esc_url( 'https://wordpress.org/plugins/' . $payje_plugin_download_url )
            );
        }

        /////////////////////////////////////////////////////////////

        // If main plugin is installed but not activated, return plugin activation URL
        if ( $is_main_plugin_installed && !$is_main_plugin_activated ) {
            return array(
                'label' => sprintf( __( 'Activate %s', 'payje' ), $main_plugin_name ),
                'url'   => wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $main_plugin_file ), 'activate-plugin_' . $main_plugin_file )
            );
        }

        // If main plugin is not installed, return plugin download URL
        if ( !$is_main_plugin_installed ) {
            return array(
                'label' => sprintf( __( 'Download %s', 'payje' ), $main_plugin_name ),
                'url'   => esc_url( 'https://wordpress.org/plugins/' . $main_plugin_download_url )
            );
        }

    }

    // Enqueue styles & scripts
    public function enqueue_scripts( $hook ) {

        wp_enqueue_style( 'payje-admin-all', PAYJE_URL . 'assets/css/admin-all.css', array(), PAYJE_VERSION, 'all' );

        wp_register_style( 'sweetalert2', PAYJE_URL . 'assets/css/sweetalert2.min.css', array(), '11.4.1', 'all' );
        wp_register_script( 'sweetalert2', PAYJE_URL . 'assets/js/sweetalert2.all.min.js', array( 'jquery' ), '11.4.1', true );

        if (!str_contains( $hook, 'payje')) {
            return;
        }

        wp_enqueue_style( 'payje-admin-global', PAYJE_URL . 'assets/css/global.min.css', array(), '3.0.23', 'all' );
        wp_enqueue_style( 'payje-admin', PAYJE_URL . 'assets/css/admin.css', array(), PAYJE_VERSION, 'all' );

        wp_enqueue_script( 'flowbite', PAYJE_URL . 'assets/js/flowbite.js', array( 'jquery' ), '1.3.4', true );
        wp_enqueue_script( 'jquery-validate', PAYJE_URL . 'assets/js/jquery.validate.min.js', array( 'jquery' ), '1.19.3', true );

        wp_enqueue_style( 'sweetalert2' );
        wp_enqueue_script( 'sweetalert2' );

        wp_enqueue_script( 'payje-admin', PAYJE_URL . 'assets/js/admin.js', array( 'jquery', 'jquery-validate', 'sweetalert2' ), PAYJE_VERSION, true );

        wp_localize_script( 'payje-admin', 'payje_login', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'payje_login_nonce' ),
        ) );

        wp_localize_script( 'payje-admin', 'payje_logout', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'payje_logout_nonce' ),
        ) );

    }

    // Process Payje account login
    public function login() {

        check_ajax_referer( 'payje_login_nonce', 'nonce' );

        $nonce    = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;
        $email    = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : null;
        $password = isset( $_POST['password'] ) ? sanitize_text_field( $_POST['password'] ) : null;
        $remember = isset( $_POST['remember'] ) ? (bool) sanitize_text_field( $_POST['remember'] ) : false;

        if ( !wp_verify_nonce( $nonce, 'payje_login_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid nonce', 'payje' ),
            ), 400 );
        }

        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'No permission to execute the action', 'payje' ),
            ), 400 );
        }

        if ( !$email || !$password ) {
            wp_send_json_error( array(
                'message' => __( 'Missing required field', 'payje' ),
            ), 400 );
        }

        try {
            
            $payje = new Payje_API();

            list( $code, $response ) = $payje->sign_in( array(
                'email'    => $email,
                'password' => $password,
            ) );

            $data = isset( $response['data'] ) ? $response['data'] : false;
            $errors = isset( $response['errors'] ) ? $response['errors'] : false;

            if ( $errors ) {
                foreach ( $errors as $error ) {
                    throw new Exception( $error[0] );
                }
            }

            if ( isset( $data['token'] ) && !empty( $data['token'] ) ) {
                payje_update_access_token( $data['token'], $remember );
            } else {
                throw new Exception( __( 'An error occured! Please try again.', 'payje' ) );
                
            }

        } catch ( Exception $e ) {
            wp_send_json_error( array(
                'message' => $e->getMessage(),
            ), 400 );
        }

        wp_send_json_success();

    }

    // Process Payje account logout
    public function logout() {

        check_ajax_referer( 'payje_logout_nonce', 'nonce' );

        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;

        if ( !wp_verify_nonce( $nonce, 'payje_logout_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid nonce', 'payje' ),
            ), 400 );
        }

        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'No permission to execute the action', 'payje' ),
            ), 400 );
        }

        payje_delete_access_token();

        wp_send_json_success();

    }

}
new Payje_Admin();
