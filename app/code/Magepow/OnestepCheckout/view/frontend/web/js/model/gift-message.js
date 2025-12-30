/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define(['jquery', 'ko', 'uiElement', 'underscore', 'mage/translate'],
    function ($, ko, uiElement, _, $t) {
        'use strict';

        var provider = uiElement();

        return function () {
            var model = {
                observables: {},
                initialize: function () {
                    this.getObservable('alreadyAdded')(false);
                    var message = window.checkoutConfig.mageConfig.giftMessageOptions.giftMessage.hasOwnProperty('orderLevel')
                        ? window.checkoutConfig.mageConfig.giftMessageOptions.giftMessage['orderLevel']
                        : null;
                    if (_.isObject(message)) {
                        this.getObservable('recipient')(message.recipient);
                        this.getObservable('sender')(message.sender);
                        this.getObservable('message')(message.message);
                        this.getObservable('alreadyAdded')(true);
                    }
                },
                getObservable: function (key) {
                    this.initObservable('message-orderLevel', key);
                    return provider[this.getUniqueKey('message-orderLevel', key)];
                },
                initObservable: function (node, key) {
                    if (node && !this.observables.hasOwnProperty(node)) {
                        this.observables[node] = [];
                    }
                    if (key && this.observables[node].indexOf(key) == -1) {
                        this.observables[node].push(key);
                        provider.observe(this.getUniqueKey(node, key));
                    }
                },
                getUniqueKey: function (node, key) {
                    return node + '-' + key;
                },
                getConfigValue: function (key) {
                    return window.checkoutConfig.mageConfig.giftMessageOptions.hasOwnProperty(key) ?
                        window.checkoutConfig.mageConfig.giftMessageOptions[key]
                        : null;
                },

                /**
                 * Check if gift message can be displayed
                 *
                 * @returns {Boolean}
                 */
                isGiftMessageAvailable: function () {
                    return this.getConfigValue('isOrderLevelGiftOptionsEnabled');
                },
                /**
                 * show message below order summary
                 * @param type
                 * @param message
                 */
                showMessage: function (type, message) {
                    var classElement = 'message ' + type;
                    $('#opc-sidebar .block.items-in-cart').before('<div class=" ' + classElement + '"> <span>' + $t(message) + '</span></div>');
                    setTimeout(function () {
                        $('#opc-sidebar .opc-block-summary .message.' + type).remove();
                    }, 3000);
                }
            };
            model.initialize();
            return model;
        }
    }
);
