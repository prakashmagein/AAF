/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define([
    'jquery',
    'ko',
    'Magento_Checkout/js/view/form/element/email',
    'Magento_Customer/js/model/customer',
    'Magepow_OnestepCheckout/js/model/one-step-checkout-data',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magepow_OnestepCheckout/js/action/check-email-availability',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/quote',
    'mage/url',
    'rjsResolver',
    'mage/validation'
], function ($, ko, Component, customer, OneStepCheckoutData, additionalValidators, checkEmailAvailability, checkoutData, quote, urlBuilder, resolver) {
    'use strict';

    var cacheKey = 'form_register_chechbox',
        allowGuestCheckout = window.checkoutConfig.mageConfig.allowGuestCheckout,
        passwordMinLength = window.checkoutConfig.mageConfig.register.dataPasswordMinLength,
        passwordMinCharacter = window.checkoutConfig.mageConfig.register.dataPasswordMinCharacterSets,
        customerEmailElement = '.form-login #customer-email';

    if (!customer.isLoggedIn() && !allowGuestCheckout) {
        OneStepCheckoutData.setData(cacheKey, true);
    }

    return Component.extend({
        defaults: {
            email: checkoutData.getInputFieldEmailValue(),
            template: 'Magepow_OnestepCheckout/form/element/email',
            isLoginVisible: false,
            listens: {
                email: ''
            }
        },
        checkDelay: 0,
        savingEmailRequest: null,
        dataPasswordMinLength: passwordMinLength,
        dataPasswordMinCharacterSets: passwordMinCharacter,

        initialize: function () {
            this._super();

            if (!!this.email()) {
                resolver(this.emailHasChanged.bind(this));
            }

            additionalValidators.registerValidator(this);
        },

        initObservable: function () {
            this._super()
                .observe({
                    isCheckboxRegisterVisible: allowGuestCheckout,
                    isRegisterVisible: OneStepCheckoutData.getData(cacheKey)
                });

            this.isRegisterVisible.subscribe(function (newValue) {
                OneStepCheckoutData.setData(cacheKey, newValue);
            });

            return this;
        },

        /**
         * Check email existing.
         */
        checkEmailAvailability: function () {
            var self = this;
            this.validateRequest();
            this.isLoading(true);
            this.checkRequest = checkEmailAvailability(this.email());
            this.checkRequest.done(function (isEmailAvailable) {
                self.isPasswordVisible(!isEmailAvailable);
            }).fail(function () {
                self.isPasswordVisible(false);
            }).always(function () {
                self.isLoading(false);
            });
        },

  
        validateEmail: function (focused) {
            var loginFormSelector = 'form[data-role=email-with-possible-login]',
                usernameSelector = loginFormSelector + ' input[name=username]',
                loginForm = $(loginFormSelector),
                validator;

            loginForm.validation();

            if (focused === false) {
                return !!$(usernameSelector).valid();
            }

            validator = loginForm.validate();
            return validator;
        },

        validate: function (type) {

            if (customer.isLoggedIn() || !this.isRegisterVisible() || this.isPasswordVisible()) {
                OneStepCheckoutData.setData('register', false);
                return true;
            }

            if (typeof type !== 'undefined') {
                var selector = $('#onestepcheckout-' + type);

                selector.parents('form').validation();
            }

            var passwordSelector = $('#onestepcheckout-password');
            passwordSelector.parents('form').validation();

            var password = !!passwordSelector.valid();
            var confirm = !!$('#onestepcheckout-password-confirmation').valid();

            var result = password && confirm;
            if (result) {
                OneStepCheckoutData.setData('register', true);
                OneStepCheckoutData.setData('password', passwordSelector.val());
            } else if (!password) {
                $('#onestepcheckout-password').focus();
            } else if (!confirm) {
                $('#onestepcheckout-password-confirmation').focus();
            }

            return result;

        },

        /** Move label element when input has value */
        hasValue: function () {
            if (window.checkoutConfig.mageConfig.isUsedMaterialDesign) {
                $(customerEmailElement).val() ? $(customerEmailElement).addClass('active') : $(customerEmailElement).removeClass('active');
            }
        }
    });
});
