/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

define(
    [
        'jquery',
        'prototype'
    ],
    function (jQuery) {
        'use strict';

        return function () {
            /**
             * Add additional field to request
             */
            window.AdminOrder.prototype.itemsUpdate = function () {
                var area = ['sidebar', 'items', 'shipping_method', 'billing_method', 'totals', 'giftmessage'];
                // prepare additional fields
                var fieldsPrepare = {update_items: 1};
                //Custom code start
                var taxRatesEl = jQuery('select[name="items_tax_rates"]' );
                //Custom code end
                var info = $('order-items_grid').select('input', 'select', 'textarea');
                for (var i = 0; i < info.length; i++) {
                    if (!info[i].disabled && (info[i].type != 'checkbox' || info[i].checked)) {
                        fieldsPrepare[info[i].name] = info[i].getValue();
                    }
                }
                fieldsPrepare = Object.extend(fieldsPrepare, this.productConfigureAddFields);
                //Custom code start
                fieldsPrepare['tax_rate_id'] = taxRatesEl.val();
                //Custom code end

                this.productConfigureSubmit('quote_items', area, fieldsPrepare);
                this.orderItemChanged = false;
            };

            window.AdminOrder.prototype.setShippingMethod = function (method) {
                var data = {};

                var customPriceElem = document.querySelector('input[name="mf-custom-shipping-price"]');
                if (customPriceElem) {
                    data['mf_custom_shipping_price'] = customPriceElem.value;
                }

                data['order[shipping_method]'] = method;
                this.loadArea([
                    'shipping_method',
                    'totals',
                    'billing_method'
                ], true, data).then(function () {
                    window.initMfCustomShippingPriceBlock();
                });
            }
        };
    }
);
