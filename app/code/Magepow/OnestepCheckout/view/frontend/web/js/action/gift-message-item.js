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
        'Magento_Checkout/js/model/quote',
        'Magepow_OnestepCheckout/js/model/resource-url-manager',
        'Magepow_OnestepCheckout/js/model/gift-message',
        'mage/storage'
    ],
    function ($,
              quote,
              resourceUrlManager,
              giftMessageModel,
              storage) {
        'use strict';

        var giftMessageItems = window.checkoutConfig.mageConfig.giftMessageOptions.giftMessage.itemLevel,
            giftMessageModel = new giftMessageModel();

        return function (data, itemId, remove) {
            return storage.post(
                resourceUrlManager.getUrlForGiftMessageItemInformation(quote, itemId),
                JSON.stringify(data)
            ).done(
                function (response) {
                    if (response == true) {
                        if (remove) {
                            delete giftMessageItems[itemId].message;
                            giftMessageModel.showMessage('success', 'Delete gift message item success.');
                            return this;
                        }
                        giftMessageItems[itemId]['message'] = data.gift_message;
                        giftMessageModel.showMessage('success', 'Update gift message item success.');
                    }
                }
            ).fail(
                function () {
                    if (remove) {
                        giftMessageModel.showMessage('error', 'Can not delete gift message item. Please try again!');
                    }
                    giftMessageModel.showMessage('error', 'Can not update gift message item. Please try again!');
                }
            )
        };
    }
);
