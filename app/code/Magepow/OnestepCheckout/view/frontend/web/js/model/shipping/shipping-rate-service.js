/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
 define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-rate-processor/new-address',
        'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/model/error-processor'
    ],
    function (quote, defaultProcessor, customerAddressProcessor, resourceUrlManager, storage, rateRegistry, shippingService, errorProcessor) {
        'use strict';

        var processors = [];

        processors.default = defaultProcessor;
        processors['customer-address'] = customerAddressProcessor;

        return {
            isAddressChange: false,
            registerProcessor: function (type, processor) {
                processors[type] = processor;
            },
            estimateShippingMethod: function () {
                var type = quote.shippingAddress().getType();
                var cache;
                cache = rateRegistry.get(quote.shippingAddress().getKey());
                if(cache){
                    storage.post(
                        resourceUrlManager.getUrlForEstimationShippingMethodsByAddressId(),
                        JSON.stringify({
                            addressId: quote.shippingAddress()['customerAddressId']
                        }),
                        false
                    ).done(function (result) {
                        rateRegistry.set(quote.shippingAddress().getKey(), result);
                        shippingService.setShippingRates(result);
                    }).fail(function (response) {
                        shippingService.setShippingRates([]);
                        errorProcessor.process(response);
                    }).always(function () {
                        shippingService.isLoading(false);
                    });
                }

                if (processors[type]) {
                    processors[type].getRates(quote.shippingAddress());
                } else {
                    processors.default.getRates(quote.shippingAddress());
                }
            }
        }
    }
);

