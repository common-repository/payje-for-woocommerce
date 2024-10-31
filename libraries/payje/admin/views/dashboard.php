<div class="wrap"><h1></h1></div>

<div class="payje-page">
    <div class="p-4 pd:mx-0">
        <div class="container max-w-screen-xl m-auto space-y-7">
            <div class="flex flex-col sm:flex-row items-center">
                <div class="w-full mt-3 sm:mr-4 sm:mt-0">
                    <h1 class="text-3xl sm:text-4xl font-bold"><?php esc_html_e( 'Dashboard', 'payje' ); ?></h1>
                    <p class="text-gray-400 mt-1"><?php printf( __( 'For further information, please visit our website: <a href="%s" class="text-primary hover:text-purple-500" target="_blank">www.edaran.com</a>', 'payje' ), 'https://www.edaran.com/' ); ?></p>
                </div>
                <img class="object-contain h-10 m-auto sm:mr-0 order-first sm:order-last" alt="<?php esc_attr_e( 'Payje logo', 'payje' ); ?>" src="<?php echo esc_attr( PAYJE_URL . 'assets/images/logo-payje.svg' ); ?>">
            </div>

            <div class="p-4 w-full text-center bg-white rounded-lg border shadow-md sm:p-8">
                <h4 class="mb-2 text-3xl font-bold text-gray-900"><?php esc_html_e( 'Complete Business Solution!', 'payje' ); ?></h4>
                <p class="mb-5 text-base text-gray-500 sm:text-lg"><?php esc_html_e( "Start using Payje with confidence. We've considered it all - so you can securely integrate and use our platform, anytime.", 'payje' ); ?></p>
                <div class="justify-center items-center space-y-4 sm:flex sm:space-y-0 sm:space-x-4">
                    <a href="<?php echo esc_attr( $payje_wc_plugin['url'] ); ?>" class="w-full sm:w-auto flex bg-gray-800 hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 text-white rounded-lg inline-flex items-center justify-center px-4 py-2.5">
                        <img class="w-10 h-10 mr-3" src="<?php echo esc_attr( PAYJE_URL . 'assets/images/logo-woocommerce.svg' ); ?>" alt="WooCommerce logo">
                        <div class="text-left">
                            <div class="mb-1 text-xs"><?php echo esc_html( $payje_wc_plugin['label'] ); ?></div>
                            <div class="-mt-1 font-sans text-sm font-semibold"><?php esc_html_e( 'Payje for WooCommerce', 'payje' ) ?></div>
                        </div>
                    </a>
                    <a href="<?php echo esc_attr( $payje_gf_plugin['url'] ); ?>" class="w-full sm:w-auto flex bg-gray-800 hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 text-white rounded-lg inline-flex items-center justify-center px-4 py-2.5">
                        <img class="w-7 h-7 mr-3" src="<?php echo esc_attr( PAYJE_URL . 'assets/images/logo-gravity-forms.svg' ); ?>" alt="Gravity Forms logo">
                        <div class="text-left">
                            <div class="mb-1 text-xs"><?php echo esc_html( $payje_gf_plugin['label'] ); ?></div>
                            <div class="-mt-1 font-sans text-sm font-semibold"><?php esc_html_e( 'Payje for Gravity Forms', 'payje' ) ?></div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="button" id="payje-logout" class="relative px-6 py-2 rounded-md inline-flex items-center justify-center disabled:opacity-50 border transition-all text-sm bg-primary hover:bg-hover-button focus:bg-click-button text-white border-transparent ring-none"><?php esc_html_e( 'Sign Out', 'payje' ); ?></button>
            </div>
        </div>
    </div>
</div>
