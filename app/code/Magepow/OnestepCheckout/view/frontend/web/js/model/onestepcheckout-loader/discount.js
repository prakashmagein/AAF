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
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-rate-processor/new-address', // Shipping rate processor for new address
        'Magento_Checkout/js/model/shipping-rate-processor/customer-address', // Shipping rate processor for customer address
        'Magento_Checkout/js/action/select-shipping-method'
    ],
    function (ko, quote, newAddressProcessor, customerAddressProcessor, selectShippingMethodAction) {
        'use strict';
        var totals = quote.getTotals(),
        couponCode = ko.observable(null);

        if (totals()) {
            couponCode(totals()['coupon_code']);
        }

        // Subscribe to changes in totals and detect when a coupon code is applied
        quote.totals.subscribe(function (newTotals) {
           if (newTotals) {
                couponCode(newTotals.coupon_code);
                reloadShippingRates();
            }
        });

        /**
         * Reload shipping methods after coupon is applied
         */
        function reloadShippingRates() {
            var shippingAddress = quote.shippingAddress();
            if (shippingAddress) {
                if (shippingAddress['customerAddressId']) {
                    customerAddressProcessor.getRates(shippingAddress);
                } else {
                    newAddressProcessor.getRates(shippingAddress);
                }
            }
        }

        return {
            isLoading: ko.observable(false),
            isAppliedCoupon: ko.observable(couponCode() != null),
            /**
             * Start full page loader action
             */
            startLoader: function () {
                this.isLoading(true);
            },
            /**
             * Stop full page loader action
             */
            stopLoader: function () {
                this.isLoading(false);
            }
        };
    }
);
