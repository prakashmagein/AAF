define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
    'Magento_Checkout/js/model/shipping-rate-processor/new-address'
], function ($, quote, rateRegistry, shippingService, customerAddressProcessor, newAddressProcessor) {
    'use strict';

    return function (originalAction) {
        $(document).on('change', '[name="city"]', function () {
            var address = quote.shippingAddress();
        
            //address.trigger_reload = new Date().getTime();
            rateRegistry.set(address.getKey(), null);
            rateRegistry.set(address.getCacheKey(), null);
            quote.shippingAddress(address);
            if (quote.isVirtual()) {
                return;
            }

            if (quote.shippingAddress().isEditable()) {
                newAddressProcessor.getRates(quote.shippingAddress());
            } else {
                customerAddressProcessor.getRates(quote.shippingAddress());
            }
        });

        return originalAction;
    };
});