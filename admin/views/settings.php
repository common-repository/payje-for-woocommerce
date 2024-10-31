<div class="wrap"><h1></h1></div>

<div class="payje-page">
    <div class="p-4 pd:mx-0">
        <div class="container max-w-screen-xl m-auto space-y-7">
            <div class="flex flex-col sm:flex-row items-center">
                <div class="w-full mt-3 sm:mr-4 sm:mt-0">
                    <h1 class="text-3xl sm:text-4xl font-bold"><?php esc_html_e( 'WooCommerce Settings', 'payje-wc' ); ?></h1>
                    <p class="text-gray-400 mt-1"><?php printf( __( 'For further information, please visit our website: <a href="%s" class="text-primary hover:text-purple-500" target="_blank">www.edaran.com</a>', 'payje-wc' ), 'https://www.edaran.com' ); ?></p>
                </div>
                <img class="object-contain h-10 m-auto sm:mr-0 order-first sm:order-last" alt="<?php esc_attr_e( 'Payje logo', 'payje-wc' ); ?>" src="<?php echo esc_attr( PAYJE_URL . 'assets/images/logo-payje.svg' ); ?>">
            </div>

            <form id="payje-wc-settings" action="" method="POST" class="divide-y">
                <div class="form-section">
                    <div class="form-group flex flex-col sm:flex-row mb-6">
                        <label for="enabled" class="w-64 text-sm font-medium text-gray-900 mb-2 sm:mt-2 sm:mb-0 block"><?php esc_html_e( 'Enable/Disable', 'payje-wc' ); ?></label>

                        <div class="w-full">
                            <label for="enabled" class="flex relative items-center cursor-pointer">
                                <input type="checkbox" id="enabled" name="enabled" class="sr-only" <?php checked( $enabled, 'yes' ); ?>>
                                <div class="w-11 h-6 bg-gray-200 rounded-full border border-gray-200 toggle-bg"></div>
                                <span class="ml-3 text-sm font-medium text-gray-900"><?php esc_html_e( 'Enable Payje', 'payje-wc' ) ?></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group flex flex-col sm:flex-row mb-6">
                        <label for="business" class="w-64 text-sm font-medium text-gray-900 mb-2 sm:mt-2 sm:mb-0 block"><?php esc_html_e( 'Business Selection', 'payje-wc' ); ?></label>

                        <?php if ( $businesses ) : ?>
                            <div class="w-full">
                                <button type="button" id="business" data-dropdown-toggle="business-items" class="inline-flex items-center bg-primary hover:bg-hover-button focus:bg-click-button text-white font-medium text-sm text-white text-center rounded-md px-4 py-2 disabled:opacity-50">
                                    <svg class="h-5 w-5 flex-none mr-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path><path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"></path></svg>
                                    <span><?php echo isset( $current_business['name'] ) ? esc_html( $current_business['name'] ) : esc_html__( 'Select a business', 'payje-wc' ); ?></span>
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <div id="business-items" class="w-64 bg-white text-base list-none rounded divide-gray-100 shadow hidden z-10">
                                    <ul class="py-1">
                                        <?php
                                        foreach ( $businesses as $item ) :
                                            $is_current_business = $item['id'] == $business_id;
                                            ?>
                                            <li class="business-data <?php echo ( $is_current_business ? 'bg-gray-100 ' : '' ); ?>text-gray-700 text-sm px-4 py-2 mb-0 block cursor-pointer" data-id="<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['name'] ); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php else : ?>
                            <span class="w-full text-gray-400 sm:mt-2 self-center block"><?php esc_html_e( 'No business found.', 'payje-wc' ); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group flex flex-col sm:flex-row mb-6">
                        <label for="environment_production" class="w-64 text-sm font-medium text-gray-900 mb-2 sm:mt-2 sm:mb-0 block"><?php esc_html_e( 'Environment', 'payje-wc' ); ?></label>

                        <div class="flex w-full space-x-10">
                            <div class="inline-flex justify-center">
                                <input type="radio" id="environment_production" name="environment" class="w-5 h-5 mt-1 bg-white shadow-sm border-white checked:border-white focus:border-white focus:ring-white focus:ring-offset-0 focus:ring-1" value="production" <?php checked( $environment, 'production' ); ?>>
                                <label for="environment_production" class="ml-2 mt-1 text-sm"><?php esc_html_e( 'Production', 'payje-wc' ); ?></label>
                            </div>
                            <div class="inline-flex justify-center">
                                <input type="radio" id="environment_sandbox" name="environment" class="w-5 h-5 mt-1 bg-white shadow-sm border-white checked:border-white focus:border-white focus:ring-white focus:ring-offset-0 focus:ring-1" value="sandbox" <?php checked( $environment, 'sandbox' ); ?>>
                                <label for="environment_sandbox" class="ml-2 mt-1 text-sm"><?php esc_html_e( 'Sandbox', 'payje-wc' ); ?></label>
                            </div>

                            <span id="environment-current" data-value="<?php echo esc_attr( $environment ); ?>"></span>
                            <span id="environment-new" data-value=""></span>
                        </div>
                    </div>

                    <div class="form-group flex flex-col sm:flex-row mb-6">
                        <label for="api_key" class="w-64 text-sm font-medium text-gray-900 mb-2 sm:mt-2 sm:mb-0 block"><?php esc_html_e( 'API Access Key', 'payje-wc' ); ?></label>
                        <input type="text" id="api_key" class="rounded-md bg-gray-50 w-full leading-normal px-4 py-2 shadow-sm text-sm border-none focus:ring-primary focus:ring-2 focus:outline-none ring-bordercolor" readonly value="<?php echo esc_attr( $api_key ); ?>">
                    </div>

                    <div class="form-group flex flex-col sm:flex-row mb-6">
                        <label for="signature_key" class="w-64 text-sm font-medium text-gray-900 mb-2 sm:mt-2 sm:mb-0 block"><?php esc_html_e( 'API Signature Key', 'payje-wc' ); ?></label>
                        <input type="text" id="signature_key" class="rounded-md bg-gray-50 w-full leading-normal px-4 py-2 shadow-sm text-sm border-none focus:ring-primary focus:ring-2 focus:outline-none ring-bordercolor" readonly value="<?php echo esc_attr( $signature_key ); ?>">
                    </div>

                    <div class="form-group flex flex-col sm:flex-row sm:items-center justify-end mb-6">
                        <span class="text-gray-400 px-5 pt-3 sm:pt-0 block order-last sm:order-first"><?php esc_html_e( 'Refetch the key if you have reset any API or Signature Key on Payje.', 'payje-wc' ); ?></span>
                        <button id="retrieve-api-credentials" class="relative w-full sm:w-auto px-6 py-2 rounded-md inline-flex items-center justify-center disabled:opacity-50 border transition-all text-sm bg-primary hover:bg-hover-button focus:bg-click-button text-white border-transparent ring-none" type="button"><?php esc_html_e( 'Retrieve Key', 'payje-wc' ); ?></button>
                    </div>

                    <div class="form-group flex flex-col sm:flex-row sm:items-center justify-end mb-6">
                        <span class="text-gray-400 px-5 pt-3 sm:pt-0 block order-last sm:order-first"><?php esc_html_e( 'Save WooCommerce webhook URL in Payje to receive payment notification.', 'payje-wc' ); ?></span>
                        <button id="set-webhook" class="relative w-full sm:w-auto px-6 py-2 rounded-md inline-flex items-center justify-center disabled:opacity-50 border transition-all text-sm bg-primary hover:bg-hover-button focus:bg-click-button text-white border-transparent ring-none" type="button"><?php esc_html_e( 'Set Webhook', 'payje-wc' ); ?></button>
                    </div>
                </div>

                <div class="form-section pt-6">
                    <div class="form-group flex flex-col sm:flex-row mb-6">
                        <label for="description" class="w-64 text-sm font-medium text-gray-900 mb-2 sm:mt-2 sm:mb-0 block"><?php esc_html_e( 'Description', 'payje-wc' ); ?></label>
                        <div class="form-field-wrapper w-full">
                            <textarea id="description" name="description" class="rounded-md bg-white w-full leading-normal px-4 py-2 shadow-sm text-sm border-none focus:ring-primary focus:ring-2 focus:outline-none ring-bordercolor" required><?php echo esc_html( $description ); ?></textarea>
                            <span class="text-gray-400 pt-2 px-5 block"><?php esc_html_e( 'This controls the description which the user sees during checkout.', 'payje-wc' ); ?></span>
                        </div>
                    </div>

                    <div class="form-group flex flex-col sm:flex-row mb-6">
                        <label for="title" class="w-64 text-sm font-medium text-gray-900 mb-2 sm:mt-2 sm:mb-0 block"><?php esc_html_e( 'Checkout Label', 'payje-wc' ); ?></label>
                        <div class="form-field-wrapper w-full">
                            <input type="text" id="title" name="title" class="rounded-md bg-white w-full leading-normal px-4 py-2 shadow-sm text-sm border-none focus:ring-primary focus:ring-2 focus:outline-none ring-bordercolor" value="<?php echo esc_attr( $title ); ?>" required>
                            <span class="text-gray-400 pt-2 px-5 block"><?php esc_html_e( "You may change the label of 'Pay Using' but you will see Payje at the end of the label.", 'payje-wc' ); ?></span>
                        </div>
                    </div>

                    <div id="errors"></div>

                    <button class="relative w-full sm:w-auto px-6 py-2 rounded-md inline-flex items-center justify-center disabled:opacity-50 border transition-all text-sm bg-primary hover:bg-hover-button focus:bg-click-button text-white border-transparent ring-none" type="submit"><?php esc_html_e( 'Save Changes', 'payje-wc' ); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
