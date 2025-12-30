/**
 * @copyright Copyright Â© 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
 define(
    [
        'jquery',
        'underscore',
        'mage/storage',
        'Magento_Checkout/js/view/shipping',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magepow_OnestepCheckout/js/action/payment-total-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/shipping-rate-service',
        'Magento_Checkout/js/model/shipping-service',
        'Magepow_OnestepCheckout/js/model/checkout-data-resolver',
        'Magepow_OnestepCheckout/js/model/address/auto-complete',
        'Magepow_OnestepCheckout/js/model/one-step-checkout-data',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/action/create-shipping-address',
        'rjsResolver',
        'mage/translate'
    ],
    function ($,
              _,
              storage,
              Component,
              quote,
              customer,
              resourceUrlManager,
              setShippingInformationAction,
              getPaymentTotalInformation,
              stepNavigator,
              additionalValidators,
              checkoutData,
              selectBillingAddress,
              selectShippingAddress,
              addressConverter,
              shippingRateService,
              shippingService,
              dataResolver,
              addressAutoComplete,
              oneStepCheckoutData,
              paymentService,
              methodConverter,
              errorProcessor,
              fullScreenLoader,
              addressList,
              createShippingAddress,
              resolver) {
        'use strict';

        dataResolver.resolveDefaultShippingMethod();

        /** Set shipping methods to collection */
        /*shippingService.setShippingRates(window.checkoutConfig.shippingMethods); */

        return Component.extend({
            defaults: {
                template: 'Magepow_OnestepCheckout/shipping-method',
                shippingFormTemplate: 'Magento_Checkout/shipping-address/form',
            },
            currentMethod: null,
            saveInAddressBook: 1,
            initialize: function () {
                this._super();
                if (!quote.shippingAddress() && addressList().length >= 1) {
                    selectShippingAddress(addressList()[0]);
                }

                stepNavigator.steps.removeAll();

                //shippingRateService.estimateShippingMethod();
                additionalValidators.registerValidator(this);

                resolver(this.afterResolveDocument.bind(this));

                return this;
            },

            initObservable: function () {
                this._super();

                quote.shippingMethod.subscribe(function (oldValue) {
                    this.currentMethod = oldValue;
                }, this, 'beforeChange');

                quote.shippingMethod.subscribe(function (newValue) {
                    var isMethodChange = ($.type(this.currentMethod) !== 'object') ? true : this.currentMethod.method_code;
                    if ($.type(newValue) === 'object' && (isMethodChange !== newValue.method_code)) {
                        setShippingInformationAction();
                    } else if (shippingRateService.isAddressChange) {
                        shippingRateService.isAddressChange = false;
                        getPaymentTotalInformation();
                    }
                }, this);

                return this;
            },

            afterResolveDocument: function () {
                addressAutoComplete.register('shipping');
                if(!quote.isVirtual() && quote.shippingAddress() && quote.shippingAddress().countryId){
                    shippingRateService.estimateShippingMethod();
                }
            },

            validate: function () {
                if (quote.isVirtual()) {
                    return true;
                }

                var shippingMethodValidationResult = true,
                    shippingAddressValidationResult = true,
                    loginFormSelector = 'form[data-role=email-with-possible-login]',
                    emailValidationResult = customer.isLoggedIn();

                if (!quote.shippingMethod()) {
                    this.errorValidationMessage($.mage.__('Please specify a shipping method.'));

                    shippingMethodValidationResult = false;
                }

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                if (this.isFormInline) {
                    this.source.set('params.invalid', false);
                    this.source.trigger('shippingAddress.data.validate');

                    if (this.source.get('shippingAddress.custom_attributes')) {
                        this.source.trigger('shippingAddress.custom_attributes.data.validate');
                    }

                    if (this.source.get('params.invalid')) {
                        shippingAddressValidationResult = false;
                    }

                    this.saveShippingAddress();
                }

                return shippingMethodValidationResult && shippingAddressValidationResult && emailValidationResult;
            },
            saveShippingAddress: function () {

                var shippingAddress = quote.shippingAddress(),
                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );
                var self = this;

                //Copy form data to quote shipping address object
                for (var field in addressData) {
                    if (addressData.hasOwnProperty(field) &&
                        shippingAddress.hasOwnProperty(field) &&
                        typeof addressData[field] != 'function' &&
                        _.isEqual(shippingAddress[field], addressData[field])
                    ) {
                        shippingAddress[field] = addressData[field];
                    } else if (typeof addressData[field] != 'function' && !_.isEqual(shippingAddress[field], addressData[field])) {
                        shippingAddress = addressData;
                        break;
                    }
                }

                if (customer.isLoggedIn()) {
                    shippingAddress.save_in_address_book = 1;
                }
                selectShippingAddress(shippingAddress);

                if (!quote.billingAddress() && quote.shippingAddress().canUseForBilling()) {
                    selectBillingAddressAction(quote.shippingAddress());
                }
                self.savaDataAdded(shippingAddress);
            },

            savaDataAdded: function(shippingAddress){
                var payload,
                    additionInformation = oneStepCheckoutData.getData();
                if (window.checkoutConfig.mageConfig.giftMessageOptions.isOrderLevelGiftOptionsEnabled) {
                    additionInformation.giftMessage = this.saveGiftMessage();
                }
                var customAttributes = {};
                if (_.isObject(quote.billingAddress().customAttributes)) {
                    _.each(quote.billingAddress().customAttributes, function (attribute, key) {
                        if (_.isObject(attribute)) {
                            customAttributes[attribute.attribute_code] = attribute.value
                        } else if (_.isString(attribute)) {
                            customAttributes[key] = attribute
                        }
                    });
                }

                payload = {
                    addressInformation: {
                        'shipping_address': shippingAddress,
                        'billing_address': quote.billingAddress(),
                        'shipping_method_code': quote.shippingMethod()['method_code'],
                        'shipping_carrier_code': quote.shippingMethod()['carrier_code']
                    },
                    customerAttributes: customAttributes,
                    additionInformation: additionInformation
                };
                storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        quote.setTotals(response.totals);
                        paymentService.setPaymentMethods(methodConverter(response['payment_methods']));
                        fullScreenLoader.stopLoader();
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                        fullScreenLoader.stopLoader();
                    }
                );
            },

            saveGiftMessage: function () {
                var giftMessage = {};
                if (!$("#onestepcheckout-gift-message").is(":checked")) $('.gift-options-content').find('input:text,textarea').val('');
                giftMessage.sender = $("#gift-message-whole-from").val();
                giftMessage.recipient = $("#gift-message-whole-to").val();
                giftMessage.message = $("#gift-message-whole-message").val();
                return JSON.stringify(giftMessage);
            },

            saveNewAddress: function () {
                var self = this;
                this.source.set('params.invalid', false);
                var addressData,
                newShippingAddress;
                
                this.source.trigger('shippingAddress.data.validate');

                if (this.source.get('shippingAddress.custom_attributes')) {
                    this.source.trigger('shippingAddress.custom_attributes.data.validate');
                }
                if (!this.source.get('params.invalid')) {
                    this._super();
                    shippingRateService.isAddressChange = true;
                    shippingRateService.estimateShippingMethod();
                
                    addressData = this.source.get('shippingAddress');
                    // if user clicked the checkbox, its value is true or false. Need to convert.
                    addressData['save_in_address_book'] = this.saveInAddressBook ? 1 : 0;

                    // New address must be selected as a shipping address
                    newShippingAddress = createShippingAddress(addressData);
                    selectShippingAddress(newShippingAddress);
                    checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                    checkoutData.setNewCustomerShippingAddress($.extend(true, {}, addressData));
                    self.savaDataAdded(addressData);
                }
            },

            getAddressTemplate: function () {
                return 'Magepow_OnestepCheckout/address/shipping-address';
            }
        });
    }
);
