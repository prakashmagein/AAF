define([
    'jquery',
    'Magento_Checkout/js/model/quote'
], function ($, quote) {
    'use strict';

    return function (Component) {
        return Component.extend({

            /**
             * Get line items
             * @returns {Array}
             */
            getLineItems: function () {
                let lineItems = this._super();

                let baseDiscountAmount = parseFloat(Math.abs(quote.totals()['base_discount_amount']).toString());
                let awBaseRewardPointsAmount = parseFloat(
                    Math.abs(quote.totals()['base_aw_reward_points_amount']).toString()
                );
                baseDiscountAmount = (baseDiscountAmount + awBaseRewardPointsAmount).toFixed(2);

                if (baseDiscountAmount > 0) {
                    let discountKeyItem = null;
                    $.each(lineItems, function (keyItem, lineItem) {
                        if (lineItem.kind === 'credit' && lineItem.name === 'Discount') {
                            discountKeyItem = keyItem;
                        }
                    });

                    let discountLineItem = {
                        'name': 'Discount',
                        'kind': 'credit',
                        'quantity': 1.00,
                        'unitAmount': baseDiscountAmount
                    };

                    if (discountKeyItem) {
                        lineItems[discountKeyItem] = discountLineItem;
                    } else {
                        lineItems = $.merge(lineItems, [discountLineItem]);
                    }
                }

                return lineItems;
            }
        });
    }
});
