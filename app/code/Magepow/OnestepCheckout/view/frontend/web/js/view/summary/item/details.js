/**
 * details
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */

define(
    [
        'underscore',
        'jquery',
        'Magento_Checkout/js/view/summary/item/details',
        'Magento_Checkout/js/model/quote',
        'Magepow_OnestepCheckout/js/action/update-item',
        'Magepow_OnestepCheckout/js/action/gift-message-item',
        'mage/url',
        'mage/translate',
        'Magento_Ui/js/modal/modal'
    ],
    function (_, $, Component, quote, updateItemAction, giftMessageItem, url, $t, modal) {
        "use strict";

        var products = window.checkoutConfig.quoteItemData,
            qtyIncrements = window.checkoutConfig.mageConfig.qtyIncrements;


        return Component.extend({
            defaults: {
                template: 'Magepow_OnestepCheckout/summary/item/details'
            },
            updateQtyDelay: 500,
            updateQtyTimeout: 0,

            getProductUrl: function (parent) {
                var item = _.find(products, function (product) {
                    return product.item_id == parent.item_id;
                });

                if (item && item.hasOwnProperty('product') &&
                    item.product.hasOwnProperty('request_path') && item.product.request_path) {
                    return url.build(item.product.request_path);
                }

                return false;
            },

            /**
             * Close popup gift message item
             */
            closePopup: function () {
                $('.action-close').trigger('click');
            },
            plusQty: function (item, event) {
                var self = this;
                clearTimeout(this.updateQtyTimeout);

                var target = $(event.target).parent().siblings(".item_qty"),
                    itemId = parseInt(target.attr("id")),
                    qty = parseInt(target.val());

                if (qtyIncrements.hasOwnProperty(itemId)) {
                    var qtyDelta = qtyIncrements[itemId];

                    qty = (Math.floor(qty / qtyDelta) + 1) * qtyDelta;
                } else {
                    qty += 1;
                }

                target.val(qty);

                this.updateQtyTimeout = setTimeout(function () {
                    self.updateItem(itemId, qty, target)
                }, this.updateQtyDelay);
            },

            minusQty: function (item, event) {
                var self = this;
                clearTimeout(this.updateQtyTimeout);

                var target = $(event.target).parent().siblings(".item_qty"),
                    itemId = parseInt(target.attr("id")),
                    qty = parseInt(target.val());

                if (qtyIncrements.hasOwnProperty(itemId)) {
                    var qtyDelta = qtyIncrements[itemId];

                    qty = (Math.ceil(qty / qtyDelta) - 1) * qtyDelta;
                } else {
                    qty -= 1;
                }

                target.val(qty);

                this.updateQtyTimeout = setTimeout(function () {
                    self.updateItem(itemId, qty, target)
                }, this.updateQtyDelay);
            },
            changeQty: function (item, event) {
                var target = $(event.target),
                    itemId = parseInt(target.attr("id")),
                    qty = parseInt(target.val());

                if (qtyIncrements.hasOwnProperty(itemId) && (qty % qtyIncrements[itemId])) {
                    var qtyDelta = qtyIncrements[itemId];

                    qty = (Math.ceil(qty / qtyDelta) - 1) * qtyDelta;
                }

                this.updateItem(itemId, qty, target);
            },

            removeItem: function (itemId) {
                this.updateItem(itemId);
            },

            updateItem: function (itemId, itemQty, target) {
                var self = this,
                    payload = {
                        item_id: itemId
                    };

                if (typeof itemQty !== 'undefined') {
                    payload['item_qty'] = itemQty;
                }

                updateItemAction(payload).fail(function (response) {
                    target.val(self.getProductQty(itemId));
                });

                return this;
            },

            getProductQty: function (itemId) {
                var item = _.find(quote.totals().items, function (product) {
                    return product.item_id == itemId;
                });

                if (item && item.hasOwnProperty('qty')) {
                    return item.qty;
                }

                return 0;
            }
        });
    }
);
