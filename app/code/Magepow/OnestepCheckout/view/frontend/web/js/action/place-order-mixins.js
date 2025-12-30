/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define([
    'jquery',
    'mage/utils/wrapper',
    'Magepow_OnestepCheckout/js/action/set-checkout-information',
], function ($, wrapper, setCheckoutInformationAction) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            var deferred = $.Deferred();

            var url = window.checkoutConfig.checkoutUrl+'index/save';
            var deliveryComment = $('#comments').val();
            var orderCommment = $('[name="order_comment"]').val();
            var deliveryTime = $('#onestepcheckout-delivery-time').val();
            var is_subscribed = $('#place-order-newsletter').val();

            var payload = {
                'order_comment': (orderCommment != undefined ) ? orderCommment : '',
                'deliveryTime': (deliveryTime != undefined ) ? deliveryTime : '',
                'deliveryComment': (deliveryComment != undefined ) ? deliveryComment : '',
                'is_subscribed': (is_subscribed != undefined ) ? is_subscribed : '',
            };
            
            $.ajax({
                url: url,
                data: payload,
                dataType: 'json',
                type: 'POST',
            }).done(
                function (response) {
                    console.log(response);
                }
            ).fail(
                function (response) {
                    console.log(response);
                }
            );

            if (paymentData && paymentData.method === 'braintree_paypal') {
                setCheckoutInformationAction().done(function () {
                    originalAction(paymentData, messageContainer).done(function (response) {
                        deferred.resolve(response);
                    }).fail(function (response) {
                        deferred.reject(response);
                    })
                }).fail(function (response) {
                    deferred.reject(response);
                })
            } else {
                return originalAction(paymentData, messageContainer).fail(function (response) {
                    if ($('.message-error').length) {
                        $('html, body').scrollTop(
                            $('.message-error:visible:first').closest('div').offset().top - $(window).height() / 2
                        );
                    }
                });
            }

            return deferred;
        });
    };
});
