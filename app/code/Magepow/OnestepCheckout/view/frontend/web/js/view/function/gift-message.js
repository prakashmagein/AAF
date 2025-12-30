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
        'uiComponent',
        'Magepow_OnestepCheckout/js/model/gift-message'
    ],
    function (ko, $, Component, giftMessageModel) {
        'use strict';
        return Component.extend({

            defaults: {
                template: 'Magepow_OnestepCheckout/review/gift-message'
            },
            formBlockVisibility: null,
            resultBlockVisibility: null,
            model: {},

            /**
             * Component init
             */
            initialize: function () {
                this._super()
                    .observe('formBlockVisibility')
                    .observe({
                        'resultBlockVisibility': false
                    });
                this.model = new giftMessageModel();
                this.isResultBlockVisible();
                this.isUseGiftMessage();
            },

            /**
             *
             * @returns {boolean}
             */
            isUseGiftMessage: function () {
                return !!window.checkoutConfig.mageConfig.giftMessageOptions.giftMessage.orderLevel.hasOwnProperty("gift_message_id");
            },

            /**
             * Is reslt block visible
             */
            isResultBlockVisible: function () {
                var self = this;

                if (this.model.getObservable('alreadyAdded')()) {
                    this.resultBlockVisibility(true);
                }
                this.model.getObservable('additionalOptionsApplied').subscribe(function (value) {
                    if (value == true) {
                        self.resultBlockVisibility(true);
                    }
                });
            },

            /**
             * @param {String} key
             * @return {*}
             */
            getObservable: function (key) {
                return this.model.getObservable(key);
            },

            /**
             * Hide\Show form block
             */
            toggleFormBlockVisibility: function () {
                if (!this.model.getObservable('alreadyAdded')()) {
                    this.formBlockVisibility(!this.formBlockVisibility());
                } else {
                    this.resultBlockVisibility(!this.resultBlockVisibility());
                }
                return true;
            },

            /**
             * @return {Boolean}
             */
            isActive: function () {
                return this.model.isGiftMessageAvailable();
            }
        });
    }
);
