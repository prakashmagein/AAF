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
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_SalesRule/js/action/set-coupon-code',
        'Magento_SalesRule/js/action/cancel-coupon',
        'Magepow_OnestepCheckout/js/model/onestepcheckout-loader/discount'
    ],
    function ($, ko, Component, quote, setCouponCodeAction, cancelCouponAction, discountLoader) {
        'use strict';
        var totals = quote.getTotals(),
            couponCode = ko.observable(null),
            isApplied = discountLoader.isAppliedCoupon;

        if (totals()) {
            couponCode(totals()['coupon_code']);
        }
        return Component.extend({
            defaults: {
                template: 'Magepow_OnestepCheckout/review/discount'
            },
            isBlockLoading: discountLoader.isLoading,
            couponCode: couponCode,

            /**
             * Applied flag
             */
            isApplied: isApplied,

            /**
             * Coupon code application procedure
             */
            apply: function() {
                if (this.validate()) {
                    setCouponCodeAction(couponCode(), isApplied);
                }
            },

            /**
             * Cancel using coupon
             */
            cancel: function() {
                if (this.validate()) {
                    couponCode('');
                    cancelCouponAction(isApplied);
                }
            },

            /**
             * Coupon form validation
             *
             * @returns {Boolean}
             */
            validate: function () {
                var form = '#discount-form';

                return $(form).validation() && $(form).validation('isValid');
            }
        });
    }
);
