/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define([
    'Magepow_OnestepCheckout/js/model/address/type/google'
], function (googleAutoComplete) {
    'use strict';

    var addressType = {
        billing: 'checkout.steps.billing-step.billingAddress.billing-address-fieldset',
        shipping: 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset'
    };

    return {
        register: function (type) {
            if (addressType.hasOwnProperty(type)) {
                switch (window.checkoutConfig.mageConfig.autocomplete.type) {
                    case 'google':
                        new googleAutoComplete(addressType[type]);
                        break;
                    case 'pca':
                        break;
                    default :
                        break;
                }
            }
        }
    };
});


