/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define(
    [
        'Magento_CheckoutAgreements/js/view/checkout-agreements',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magepow_OnestepCheckout/js/model/agreement/agreement-validator'
    ],
    function (Component, additionalValidators, agreementValidator) {
        'use strict';

        additionalValidators.registerValidator(agreementValidator);
        return Component.extend({
        });
    }
);
