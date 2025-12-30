define(
    [
        'ko',
        'uiComponent',
        'Magepow_OnestepCheckout/js/model/one-step-checkout-data'
    ],
    function (ko, Component, OneStepCheckoutData) {
        "use strict";

        var cacheKey = 'is_subscribed';

        return Component.extend({
            defaults: {
                template: 'Magepow_OnestepCheckout/summary/newsletter'
            },
            initObservable: function () {
                this._super()
                    .observe({
                        isRegisterNewsletter: (typeof OneStepCheckoutData.getData(cacheKey) === 'undefined') ? window.checkoutConfig.mageConfig.newsletterDefault : OneStepCheckoutData.getData(cacheKey)
                    });
                OneStepCheckoutData.setData(cacheKey, this.isRegisterNewsletter());
                this.isRegisterNewsletter.subscribe(function (newValue) {
                    OneStepCheckoutData.setData(cacheKey, newValue);
                });

                return this;
            }
        });
    }
);
