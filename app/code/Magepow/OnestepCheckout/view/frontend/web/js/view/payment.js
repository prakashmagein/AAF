/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/view/payment',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magepow_OnestepCheckout/js/model/checkout-data-resolver',
        'Magepow_OnestepCheckout/js/model/payment-service',
        'mage/url',
        'mage/translate'
        
    ],
    function (ko,
              $,
              Component,
              quote,
              stepNavigator,
              additionalValidators,
              DataResolver,
              PaymentService,
              urlBuilder) {
        'use strict';

        DataResolver.resolveDefaultPaymentMethod();

        return Component.extend({
            defaults: {
                template: 'Magepow_OnestepCheckout/payment'
            },
            isLoading: PaymentService.isLoading,
            errorValidationMessage: ko.observable(false),

            initialize: function () {
                var self = this;

                this._super();

                stepNavigator.steps.removeAll();

                additionalValidators.registerValidator(this);

                quote.paymentMethod.subscribe(function () {
                    self.errorValidationMessage(false);
                    if (window.paymentGa4 !== undefined) {
                        if (window.paymentGa4 === quote.paymentMethod.method) {
                            return;
                        }
                    }
                    $.ajax({
                        url: urlBuilder.build('datalayerevents/select/payment'),
                        data: {
                            method: quote.paymentMethod().method
                        },
                        type: 'post',
                        dataType: "json",
                        cache: false,
                        success: function (res) {
                            let element = $('.gwl_add_payment_info');
                            if (element.length > 0 ) {
                                element.remove();
                            }
                            if (typeof(res.output) != "undefined"){
                                if ($('#checkout-step-payment').length > 0) {
                                    $('#checkout-step-payment').append(res.output);
                                }
                                if ($('#multishipping-billing-form').length > 0) {
                                    $('#multishipping-billing-form').append(res.output);
                                }
                                window.paymentGa4 = quote.paymentMethod.method;
                            }
                        },
                        error: function (res) {
                            console.log('send data event add payment info fail');
                        }
                    });
                });

                return this;
            },

            validate: function () {
                if (!quote.paymentMethod()) {
                    this.errorValidationMessage($.mage.__('Please specify a payment method.'));

                    return false;
                }

                return true;
            }
        });
    }
);
