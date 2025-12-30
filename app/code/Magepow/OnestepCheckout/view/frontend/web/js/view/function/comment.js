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
        'uiComponent',
        'Magepow_OnestepCheckout/js/model/one-step-checkout-data'
    ],
    function (ko, Component, OneStepCheckoutData) {
        "use strict";

        var cacheKey = 'deliveryComment';

        return Component.extend({
            defaults: {
                template: 'Magepow_OnestepCheckout/review/comment'
            },
            commentValue: ko.observable(),
            initialize: function () {
                this._super();

                this.commentValue(OneStepCheckoutData.getData(cacheKey));

                this.commentValue.subscribe(function (newValue) {
                    OneStepCheckoutData.setData(cacheKey, newValue);
                });

                return this;
            }
        });
    }
);
