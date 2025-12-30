define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'mage/url'
], function ($, wrapper, quote, urlBuilder) {
    'use strict';

    return function (selectPaymentMethodAction) {
        return wrapper.wrap(selectPaymentMethodAction, function (originalSelectPaymentMethodAction, paymentMethod) {

            originalSelectPaymentMethodAction(paymentMethod);

            if (paymentMethod === null) {
                return;
            }

            if (window.paymentGa4 !== undefined) {
                if (window.paymentGa4 === paymentMethod.method) {
                    return;
                }
            }

            $.ajax({
                url: urlBuilder.build('datalayerevents/select/payment'),
                data: {
                    method: paymentMethod.method
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
                        window.paymentGa4 = paymentMethod.method;
                    }
                },
                error: function (res) {
                    console.log('send data event add payment info fail');
                }
            });
        });
    };

});
