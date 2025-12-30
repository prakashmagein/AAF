/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */

define([
    'jquery',
    'Magepow_OnestepCheckout/js/action/set-checkout-information',
    'Magepow_OnestepCheckout/js/model/braintree-paypal',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/model/quote',
    'underscore'
], function ($, setCheckoutInformationAction, braintreePaypalModel, additionalValidators, quote, _) {
    'use strict';
    return function (BraintreePaypalComponent) {
        return BraintreePaypalComponent.extend({
            /**
             * Set list of observable attributes
             * @returns {exports.initObservable}
             */
            initObservable: function () {
                var self = this;

                this._super();
                // for each component initialization need update property
                this.isReviewRequired = braintreePaypalModel.isReviewRequired;
                this.customerEmail = braintreePaypalModel.customerEmail;
                this.active = braintreePaypalModel.active;

                return this;
            },
            /**
             * Get shipping address
             * @returns {Object}
             */
            getShippingAddress: function () {
                var address = quote.shippingAddress();
                if (!address) {
                    address = {};
                }
                if (!address.street) {
                    address.street = ['', ''];
                }
                if (address.postcode === null) {
                    return {};
                }

                return {
                    recipientName: address.firstname + ' ' + address.lastname,
                    streetAddress: address.street[0],
                    locality: address.city,
                    countryCodeAlpha2: address.countryId,
                    postalCode: address.postcode,
                    region: address.regionCode,
                    phone: address.telephone,
                    editable: this.isAllowOverrideShippingAddress()
                };
            }
        })
    }
});
