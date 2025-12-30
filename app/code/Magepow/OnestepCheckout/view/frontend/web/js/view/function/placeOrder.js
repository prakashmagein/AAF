/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define(
    [
        'jquery',
        'underscore',
        'ko',
        'uiComponent',
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Customer/js/customer-data',
        'Magepow_OnestepCheckout/js/action/set-checkout-information',
        'Magepow_OnestepCheckout/js/model/braintree-paypal'
    ],
    function ($,
              _,
              ko,
              Component,
              registry,
              quote,
              additionalValidators,
              customerData,
              setCheckoutInformationAction,
              braintreePaypalModel) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'Magepow_OnestepCheckout/review/place-order',
                visibleBraintreeButton: false,
            },
            braintreePaypalModel: braintreePaypalModel,
            selectors: {
                default: '#co-payment-form .payment-method._active button.action.primary.checkout'
            },
            initialize: function () {
                this._super();
                var self = this;
                quote.paymentMethod.subscribe(function (value) {
                    self.processVisiblePlaceOrderButton();
                });

                registry.async(this.getPaymentPath('braintree_paypal'))
                (this.asyncBraintreePaypal.bind(this));

                return this;
            },

            initObservable: function () {
                var self = this;

                this._super()
                    .observe(['visibleBraintreeButton']);

                return this;
            },
            asyncBraintreePaypal: function () {
                this.processVisiblePlaceOrderButton();
            },
            isBraintreeNewVersion: function () {
                var component = this.getBraintreePaypalComponent();
                return component
                    && typeof component.isReviewRequired == "function"
                    && typeof component.getButtonTitle == "function";
            },
            processVisiblePlaceOrderButton: function () {
                this.visibleBraintreeButton(this.checkVisiblePlaceOrderButton());
            },
            checkVisiblePlaceOrderButton: function () {
                return this.getBraintreePaypalComponent()
                    && this.isPaymentBraintreePaypal();
            },
            placeOrder: function () {
                var self = this;
                if (additionalValidators.validate()) {
                    this.preparePlaceOrder().done(function () {
                        self._placeOrder();
                    });
                } else {
                    var offsetHeight = $(window).height() / 2,
                        errorMsgSelector = $('#maincontent .mage-error:visible:first').closest('.field');
                    errorMsgSelector = errorMsgSelector.length ? errorMsgSelector : $('#maincontent .field-error:visible:first').closest('.field');
                    if (errorMsgSelector.length) {
                        if (errorMsgSelector.find('select').length) {
                            $('html, body').scrollTop(
                                errorMsgSelector.find('select').offset().top - offsetHeight
                            );
                            errorMsgSelector.find('select').focus();
                        } else if (errorMsgSelector.find('input').length) {
                            $('html, body').scrollTop(
                                errorMsgSelector.find('input').offset().top - offsetHeight
                            );
                            errorMsgSelector.find('input').focus();
                        }
                    } else if ($('.message-error:visible').length) {
                        $('html, body').scrollTop(
                            $('.message-error:visible:first').closest('div').offset().top - offsetHeight
                        );
                    }
                }

                return this;
            },

            brainTreePaypalPlaceOrder: function () {
                var component = this.getBraintreePaypalComponent();
                if (component && additionalValidators.validate()) {
                    component.placeOrder.apply(component, arguments);
                }

                return this;
            },

            brainTreePayWithPayPal: function () {
                var component = this.getBraintreePaypalComponent();
                if (component && additionalValidators.validate()) {
                    component.payWithPayPal.apply(component, arguments);
                }

                return this;
            },
            preparePlaceOrder: function (scrollTop) {
                var scrollTop = scrollTop !== undefined ? scrollTop : true;
                var deferer = $.when(setCheckoutInformationAction());

                return scrollTop ? deferer.done(function () {
                    $("body").animate({scrollTop: 0}, "slow");
                }) : deferer;
            },

            getPaymentPath: function (paymentMethodCode) {
                return 'checkout.steps.billing-step.payment.payments-list.' + paymentMethodCode;
            },

            getPaymentMethodComponent: function (paymentMethodCode) {
                return registry.get(this.getPaymentPath(paymentMethodCode));
            },

            isPaymentBraintreePaypal: function () {
                return quote.paymentMethod() && quote.paymentMethod().method === 'braintree_paypal';
            },

            getBraintreePaypalComponent: function () {
                return this.getPaymentMethodComponent('braintree_paypal');
            },

            _placeOrder: function () {
                    $(this.selectors.default).trigger('click');
                    customerData.invalidate(['customer']);
            },
            checkPalpal: function(){
                var customer = customerData.get('customer');
                var checkPalpal = customerData.get('checkout-data');
                if(typeof customer().firstname == 'undefined' && quote.paymentMethod().method === 'paypal_express'){
                    return true;                   
                }
                return false;  
            },

            clickPaypal: function(){
                $(this.selectors.default).trigger('click');
                customerData.invalidate(['customer']);
            },

            

            isPlaceOrderActionAllowed: function () {
                return true;
            }
        });
    }
);
