jQuery(document).ready(function($) {
    // Handle enable toggle
    $('#payje-wc-settings label[for=enabled] input[type=checkbox]').on('change', function(e) {
        var enabled = $(this).prop('checked') ? 'yes' : 'no';

        $.ajax({
            url: payje_wc_update_settings.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'payje_wc_update_settings',
                nonce: payje_wc_update_settings.nonce,
                enabled: enabled,
            },
            beforeSend: function() {
                $('body').css('cursor', 'wait');
            },
            success: function(response) {
                $('body').css('cursor', 'auto');
            },
            error: function(xhr) {
                $('body').css('cursor', 'auto');

                var error = JSON.parse(xhr.responseText);

                if (error && error.data && error.data.message) {
                    var message = '<span class="font-medium">Error!</span> ' + error.data.message + '.';
                } else {
                    var message = 'An error occured! Please try again.';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: message,
                    timer: 3000,
                });
            }
        });

        e.preventDefault();
    });

    // Handle business selection
    $('#payje-wc-settings #business-items .business-data').on('click', function(e) {
        var selected_li            = $(this),
            selected_business_name = selected_li.text(),
            selected_business_id   = selected_li.data('id');

        if (selected_li.hasClass('bg-gray-100')) {
            return false;
        }

        if (!selected_business_id) {
            return false;
        }

        var btn = $('#payje-wc-settings #business');

        $.ajax({
            url: payje_wc_update_settings.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'payje_wc_update_settings',
                nonce: payje_wc_update_settings.nonce,
                business_id: selected_business_id,
            },
            beforeSend: function() {
                btn.prop('disabled', true);
                btn.css('cursor', 'wait');
            },
            success: function(response) {
                btn.prop('disabled', false);
                btn.css('cursor', 'pointer');

                if (response.success !== undefined && response.success == true) {
                    $('#payje-wc-settings #business-items .business-data.bg-gray-100').removeClass('bg-gray-100 cursor-not-allowed').addClass('cursor-pointer');
                    selected_li.removeClass('cursor-pointer').addClass('bg-gray-100 cursor-not-allowed');

                    btn.find('span').text(selected_business_name);

                    // Retrieve API credentials on success
                    $('#payje-wc-settings #retrieve-api-credentials').trigger('click');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                btn.css('cursor', 'pointer');

                var error = JSON.parse(xhr.responseText);

                if (error && error.data && error.data.message) {
                    var message = '<span class="font-medium">Error!</span> ' + error.data.message + '.';
                } else {
                    var message = 'An error occured! Please try again.';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: message,
                    timer: 3000,
                });
            }
        });

        e.preventDefault();
    });

    // Handle update environment
    $('#payje-wc-settings input[name=environment]').on('change', function(e) {
        var input_value = $(this).val();

        var current_env = $('#environment-current'),
            new_env     = $('#environment-new');

        // Set new environment
        new_env.data('value', input_value);

        // Get current and new environment
        var current_value = current_env.data('value'),
            new_value     = $('#environment-new').data('value');

        Swal.fire({
            icon: 'warning',
            title: 'Change Environment?',
            html: 'Are you sure you want to change the environment from <span class="text-primary"><span class="capitalize">' + current_value + '</span> to <span class="capitalize">' + new_value + '</span></span>?',
            showCancelButton: true,
            confirmButtonText: 'Yes, continue',
            cancelButtonText: 'No, cancel',
            reverseButtons: true,
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: payje_wc_update_settings.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'payje_wc_update_settings',
                        nonce: payje_wc_update_settings.nonce,
                        environment: input_value,
                    },
                    beforeSend: function() {
                        $('body').css('cursor', 'wait');
                    },
                    success: function(response) {
                        $('body').css('cursor', 'auto');

                        if (response.success !== undefined && response.success == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Save Confirmed!',
                                text: 'Your changes have been successfully saved.',
                                timer: 3000,
                            });

                            // Set current environment
                            current_env.data('value', input_value);
                        }
                    },
                    error: function(xhr) {
                        $('body').css('cursor', 'auto');

                        var error = JSON.parse(xhr.responseText);

                        if (error && error.data && error.data.message) {
                            var message = '<span class="font-medium">Error!</span> ' + error.data.message + '.';
                        } else {
                            var message = 'An error occured! Please try again.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: message,
                            timer: 3000,
                        });

                        $('input[id=environment_' + new_value + ']').prop('checked', false);
                        $('input[id=environment_' + current_value + ']').prop('checked', true);
                    }
                });

            } else {
                $('input[id=environment_' + new_value + ']').prop('checked', false);
                $('input[id=environment_' + current_value + ']').prop('checked', true);
            }
        });

        e.preventDefault();
    });

    // Handle retrieve API credentials
    $('#payje-wc-settings #retrieve-api-credentials').on('click', function(e) {
        var btn                 = $(this),
            btn_text            = btn.text(),
            api_key_input       = $('#api_key'),
            signature_key_input = $('#signature_key');

        $.ajax({
            url: payje_wc_retrieve_api_credentials.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'payje_wc_retrieve_api_credentials',
                nonce: payje_wc_retrieve_api_credentials.nonce,
            },
            beforeSend: function() {
                btn.prop('disabled', true);
                btn.css('cursor', 'wait');
                btn.prepend('<svg width="73" height="72" viewBox="0 0 73 72" fill="none" xmlns="http://www.w3.org/2000/svg" class="animate-spin text-white h-5 w-5 mr-2 text-white"><path opacity="0.5" d="M36.5 6C30.5666 6 24.7664 7.75947 19.8329 11.0559C14.8994 14.3524 11.0543 19.0377 8.78363 24.5195C6.513 30.0013 5.9189 36.0333 7.07646 41.8527C8.23401 47.6721 11.0912 53.0176 15.2868 57.2132C19.4824 61.4088 24.8279 64.266 30.6473 65.4236C36.4667 66.5811 42.4987 65.987 47.9805 63.7164C53.4623 61.4458 58.1476 57.6006 61.4441 52.6671C64.7405 47.7336 66.5 41.9334 66.5 36C66.5 32.0603 65.724 28.1593 64.2164 24.5195C62.7088 20.8797 60.499 17.5726 57.7132 14.7868C54.9274 12.001 51.6203 9.79126 47.9805 8.28361C44.3407 6.77597 40.4397 6 36.5 6ZM36.5 60C31.7533 60 27.1131 58.5924 23.1663 55.9553C19.2195 53.3181 16.1434 49.5698 14.3269 45.1844C12.5104 40.799 12.0351 35.9734 12.9612 31.3178C13.8872 26.6623 16.173 22.3859 19.5294 19.0294C22.8859 15.673 27.1623 13.3872 31.8178 12.4612C36.4734 11.5351 41.299 12.0104 45.6844 13.8269C50.0698 15.6434 53.8181 18.7195 56.4553 22.6663C59.0924 26.6131 60.5 31.2532 60.5 36C60.5 42.3652 57.9714 48.4697 53.4706 52.9706C48.9697 57.4714 42.8652 60 36.5 60Z" fill="currentColor"></path><path d="M60.5 36H66.5C66.5 32.0603 65.724 28.1593 64.2164 24.5195C62.7087 20.8797 60.499 17.5726 57.7132 14.7868C54.9274 12.001 51.6203 9.79126 47.9805 8.28361C44.3407 6.77597 40.4397 6 36.5 6V12C42.8652 12 48.9697 14.5286 53.4706 19.0294C57.9714 23.5303 60.5 29.6348 60.5 36Z" fill="currentColor"></path></svg>');
            },
            success: function(response) {
                btn.prop('disabled', false);
                btn.css('cursor', 'pointer');
                btn.html(btn_text);

                if (response.data !== undefined) {
                    if (response.data.signature_key !== undefined) {
                        api_key_input.val(response.data.api_key);
                    }

                    if (response.data.signature_key !== undefined) {
                        signature_key_input.val(response.data.signature_key);
                    }
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                btn.css('cursor', 'pointer');
                btn.html(btn_text);

                var error = JSON.parse(xhr.responseText);

                if (error && error.data && error.data.message) {
                    var message = '<span class="font-medium">Error!</span> ' + error.data.message + '.';
                } else {
                    var message = 'An error occured! Please try again.';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: message,
                    timer: 3000,
                });
            }
        });

        e.preventDefault();
    });

    // Handle set webhook
    $('#payje-wc-settings #set-webhook').on('click', function(e) {
        var btn      = $(this),
            btn_text = btn.text();

        $.ajax({
            url: payje_wc_set_webhook.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'payje_wc_set_webhook',
                nonce: payje_wc_set_webhook.nonce,
            },
            beforeSend: function() {
                btn.prop('disabled', true);
                btn.css('cursor', 'wait');
                btn.prepend('<svg width="73" height="72" viewBox="0 0 73 72" fill="none" xmlns="http://www.w3.org/2000/svg" class="animate-spin text-white h-5 w-5 mr-2 text-white"><path opacity="0.5" d="M36.5 6C30.5666 6 24.7664 7.75947 19.8329 11.0559C14.8994 14.3524 11.0543 19.0377 8.78363 24.5195C6.513 30.0013 5.9189 36.0333 7.07646 41.8527C8.23401 47.6721 11.0912 53.0176 15.2868 57.2132C19.4824 61.4088 24.8279 64.266 30.6473 65.4236C36.4667 66.5811 42.4987 65.987 47.9805 63.7164C53.4623 61.4458 58.1476 57.6006 61.4441 52.6671C64.7405 47.7336 66.5 41.9334 66.5 36C66.5 32.0603 65.724 28.1593 64.2164 24.5195C62.7088 20.8797 60.499 17.5726 57.7132 14.7868C54.9274 12.001 51.6203 9.79126 47.9805 8.28361C44.3407 6.77597 40.4397 6 36.5 6ZM36.5 60C31.7533 60 27.1131 58.5924 23.1663 55.9553C19.2195 53.3181 16.1434 49.5698 14.3269 45.1844C12.5104 40.799 12.0351 35.9734 12.9612 31.3178C13.8872 26.6623 16.173 22.3859 19.5294 19.0294C22.8859 15.673 27.1623 13.3872 31.8178 12.4612C36.4734 11.5351 41.299 12.0104 45.6844 13.8269C50.0698 15.6434 53.8181 18.7195 56.4553 22.6663C59.0924 26.6131 60.5 31.2532 60.5 36C60.5 42.3652 57.9714 48.4697 53.4706 52.9706C48.9697 57.4714 42.8652 60 36.5 60Z" fill="currentColor"></path><path d="M60.5 36H66.5C66.5 32.0603 65.724 28.1593 64.2164 24.5195C62.7087 20.8797 60.499 17.5726 57.7132 14.7868C54.9274 12.001 51.6203 9.79126 47.9805 8.28361C44.3407 6.77597 40.4397 6 36.5 6V12C42.8652 12 48.9697 14.5286 53.4706 19.0294C57.9714 23.5303 60.5 29.6348 60.5 36Z" fill="currentColor"></path></svg>');
            },
            success: function(response) {
                btn.prop('disabled', false);
                btn.css('cursor', 'pointer');
                btn.html(btn_text);

                Swal.fire({
                    icon: 'success',
                    title: 'Webhook Set!',
                    text: 'Your WooCommerce webhook URL have been successfully saved in Payje.',
                    timer: 3000,
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                btn.css('cursor', 'pointer');
                btn.html(btn_text);

                var error = JSON.parse(xhr.responseText);

                if (error && error.data && error.data.message) {
                    var message = '<span class="font-medium">Error!</span> ' + error.data.message + '.';
                } else {
                    var message = 'An error occured! Please try again.';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: message,
                    timer: 3000,
                });
            }
        });

        e.preventDefault();
    });

    // Handle update settings
    $('#payje-wc-settings').validate({
        rules: {
            description: { required: true },
            title: { required: true },
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('text-xs text-error pt-2 px-5 block');
            element.closest('.form-group .form-field-wrapper').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('ring-1 ring-error focus:ring-error');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('ring-1 ring-error focus:ring-error');
        },
        submitHandler: function(form) {
            var form            = $(form),
                submit_btn      = form.find('button[type=submit]'),
                submit_btn_text = submit_btn.text(),
                title           = form.find('#title').val(),
                description     = form.find('#description').val(),
                errors          = form.find('#errors');

            $.ajax({
                url: payje_wc_update_settings.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'payje_wc_update_settings',
                    nonce: payje_wc_update_settings.nonce,
                    title: title,
                    description: description,
                },
                beforeSend: function() {
                    submit_btn.prop('disabled', true);
                    submit_btn.css('cursor', 'wait');
                    submit_btn.prepend('<svg width="73" height="72" viewBox="0 0 73 72" fill="none" xmlns="http://www.w3.org/2000/svg" class="animate-spin text-white h-5 w-5 mr-2 text-white"><path opacity="0.5" d="M36.5 6C30.5666 6 24.7664 7.75947 19.8329 11.0559C14.8994 14.3524 11.0543 19.0377 8.78363 24.5195C6.513 30.0013 5.9189 36.0333 7.07646 41.8527C8.23401 47.6721 11.0912 53.0176 15.2868 57.2132C19.4824 61.4088 24.8279 64.266 30.6473 65.4236C36.4667 66.5811 42.4987 65.987 47.9805 63.7164C53.4623 61.4458 58.1476 57.6006 61.4441 52.6671C64.7405 47.7336 66.5 41.9334 66.5 36C66.5 32.0603 65.724 28.1593 64.2164 24.5195C62.7088 20.8797 60.499 17.5726 57.7132 14.7868C54.9274 12.001 51.6203 9.79126 47.9805 8.28361C44.3407 6.77597 40.4397 6 36.5 6ZM36.5 60C31.7533 60 27.1131 58.5924 23.1663 55.9553C19.2195 53.3181 16.1434 49.5698 14.3269 45.1844C12.5104 40.799 12.0351 35.9734 12.9612 31.3178C13.8872 26.6623 16.173 22.3859 19.5294 19.0294C22.8859 15.673 27.1623 13.3872 31.8178 12.4612C36.4734 11.5351 41.299 12.0104 45.6844 13.8269C50.0698 15.6434 53.8181 18.7195 56.4553 22.6663C59.0924 26.6131 60.5 31.2532 60.5 36C60.5 42.3652 57.9714 48.4697 53.4706 52.9706C48.9697 57.4714 42.8652 60 36.5 60Z" fill="currentColor"></path><path d="M60.5 36H66.5C66.5 32.0603 65.724 28.1593 64.2164 24.5195C62.7087 20.8797 60.499 17.5726 57.7132 14.7868C54.9274 12.001 51.6203 9.79126 47.9805 8.28361C44.3407 6.77597 40.4397 6 36.5 6V12C42.8652 12 48.9697 14.5286 53.4706 19.0294C57.9714 23.5303 60.5 29.6348 60.5 36Z" fill="currentColor"></path></svg>');
                    errors.html('');
                },
                success: function(response) {
                    submit_btn.prop('disabled', false);
                    submit_btn.css('cursor', 'pointer');
                    submit_btn.html(submit_btn_text);

                    if (response.success !== undefined && response.success == true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Save Confirmed!',
                            text: 'Your changes have been successfully saved.',
                            timer: 3000,
                        });
                    }
                },
                error: function(xhr) {
                    submit_btn.prop('disabled', false);
                    submit_btn.css('cursor', 'pointer');
                    submit_btn.html(submit_btn_text);

                    var error = JSON.parse(xhr.responseText);

                    if (error && error.data && error.data.message) {
                        var message = '<span class="font-medium">Error!</span> ' + error.data.message + '.';
                    } else {
                        var message = 'An error occured! Please try again.';
                    }

                    errors.html(
                        '<div class="flex p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-200 rounded-lg" role="alert">' +
                            '<svg class="inline flex-shrink-0 mr-3 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>' +
                            '<div>' + message + '</div>' +
                        '</div>'
                    );
                }
            });
        }
    });
});
