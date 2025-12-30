/**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */

/* @api */
define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/quote'
], function ($,Component,quote) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Magedelight_SMSProfile/payment/cashondelivery'
        },
        /**
         * Init component
         */
        initialize: function () {
              var self = this;
              this._super();

        },
        getData: function () {
             return {
                    'method': this.item.method,
                    'po_number': null,
                    'additional_data': {
                        "codotp": $(document).find("#cashondelivery_codotp").val()
                    }
            };
        },

        /**
         * Returns payment method instructions.
         *
         * @return {*}
         */
        getInstructions: function () {
            return window.checkoutConfig.payment.instructions[this.item.method];
        },
        getResendTime: function () {
            return window.checkoutConfig.resendtime;
        },

        getResendTimeLimit: function () {
            return window.checkoutConfig.otpresend_limit;
        }

        
    });
    
});
