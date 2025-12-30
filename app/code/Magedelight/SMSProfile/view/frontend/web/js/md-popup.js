define([
    "jquery",
    'mage/translate',
    'underscore',
    'Magento_Ui/js/modal/modal',
    'mage/url'
], function ($,$t, _,modal,url) {
    'use strict';

    $.widget('mage.mdLoginPopup', {
        mdoptions: {
            fields: {
                popupContainer: '[data-md-js="md-login-container"]',
                login: 'button.checkout.primary',
                createAccount: 'a[href*="customer/account/create"]',
                mdTabContainer:'[data-md-js="md-tab-container"]',
                popupForgotLink: '#md-login-content .action.remind',
                mdTabWrapper: '[data-md-js="md-tabs-wrapper"]',
                mdTabWrapperForgot: '[data-md-js="md-tabs-wrapper-forgot"]',
                form:'[data-md-js="md-login-container"] .form',
                passwordInputType:'password',
                textInputType:'text',
                showPassword :'.md-login-popup #show-password',
                passwordSelector:'.md-login-popup #pass'
            }
        },

        _create: function () {
            this.initObservable();
        },

        initObservable: function () {

            var self = this;

            /* login links*/
            $(self.mdoptions.fields.login).on('click', function (event) {
                self.openPopupModal(0);
                event.preventDefault();
                return false;
                
            });
            
            var minicart = $('[data-block="minicart"]');
            minicart.on('click', '#top-cart-btn-checkout', function (event) {
                self.openPopupModal(0);
                event.preventDefault();
                return false;
                
            });

            /* create account links*/
            // $(self.mdoptions.fields.createAccount).prop('href', '#').on('click', function (event) {
            //     self.openPopupModal(1);
            //     $('.md-register-content .iti__flag-container').show();
            //     event.preventDefault();
            //     return false;
            // });

            /* forgot links*/
            // $(self.mdoptions.fields.popupForgotLink).unbind('click').on('click', function (event) {
            //     self.toggleWrappers();
            //     event.preventDefault();
            //     return false;
            // });

            /* ajax form submit */
            $(self.mdoptions.fields.form).unbind('submit').on('submit', function (event) {
                var form = $(this);
                if (form.valid()) {
                    form.find('button.action').prop('disabled', true);
                    self.submitFormWithAjax(form);
                }
                event.preventDefault();
                return false;
            });

            /* login hide/show password */
            $(document).on("click", self.mdoptions.fields.showPassword, function (event) {
                if ($(this).is(':checked')) {
                    self.showPassword(true);
                } else {
                    self.showPassword(false);
                }
            });
        },

        openPopupModal: function (activeTabIndex) {

            $(this.mdoptions.fields.mdTabWrapperForgot).hide();
            $(this.mdoptions.fields.mdTabWrapper).show();

            var mdoptions = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                buttons: false,
                modalClass: 'md-popup md-smsprofile-popup'
            };

            $(this.mdoptions.fields.popupContainer).modal(mdoptions).modal('openModal');
            $('.modal-inner-wrap').addClass('md-smsprofile-popup');
            
            if ($('html').hasClass('nav-open')) {
                $('.navigation > .ui-menu').menu('toggle');
            }
            
            $(this.mdoptions.fields.mdTabContainer).tabs('activate', activeTabIndex);
        },

        toggleWrappers: function () {
            $(this.mdoptions.fields.mdTabWrapper).toggle();
            $(this.mdoptions.fields.mdTabWrapperForgot).toggle();
        },

        submitFormWithAjax: function (form) {
            var self = this;
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'html',
                showLoader: true,
                success: function (response) {
                    $('.md-error span').text('');
                    $('.md-success span').text('');
                    var cookieMessages = $.cookieStorage.get('mage-messages');
                    $.cookieStorage.set('mage-messages', '');
                    if (cookieMessages.length) {
                        var flag = true;
                        $(cookieMessages).each(function (i, m) {
                            if (m.type === 'error') {
                                flag = false;
                            }
                        });

                        if (!flag) {
                            form.find('button.action').prop('disabled', false);
                            $('.md-error span').append($.parseHTML(cookieMessages[0].text));
                            return;
                        }
                    }

                    if (cookieMessages.length) {
                        $('.md-success').html(cookieMessages[0].text);
                    } else {
                        if (form.hasClass('form-login')) {
                            if (response.indexOf('customer/account/logout') !== -1) {
                                $('.md-success span').text(($t('You have successfully logged in.')));
                            }
                        } else if (form.hasClass('form-create-account')) {
                            $('.md-success span').text(($t('Thank you for registering with us.')));
                        }
                    }
                    setTimeout(function () {
                        // window.location.reload(true);
                         window.location.href = url.build('onestepcheckout');
                    }, 2000);
                },
                error: function () {
                    $('.md-error').html();
                    $('.md-error').html($t('Sorry, an unspecified error occurred. Please try again.'));
                    setTimeout(function () {
                        window.location.reload(true);
                    }, 2000);
                }
            });
        }        
    });

    return $.mage.mdLoginPopup;
});
