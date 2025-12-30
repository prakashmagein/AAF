define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper,quote) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction, messageContainer) {

            var shippingAddress = quote.shippingAddress();

            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }

            if (shippingAddress.customAttributes != undefined) {
                $.each(shippingAddress.customAttributes , function( key, value ) {

                    if($.isPlainObject(value)){
                        value = value['value'];
                        key = this.attribute_code;
                    }
                    if(key == "district"){
                        shippingAddress['customAttributes'][key] = value;
                        shippingAddress['extension_attributes'][key] = value;
                    }
                    if(key == "house_description"){
                        shippingAddress['customAttributes'][key] = value;
                        shippingAddress['extension_attributes'][key] = value;
                    }

                });
            }else{
                
                if(shippingAddress.postcode != null){
                    //console.log("ddddddd");
                    //location.reload();
                }
            }

            return originalAction(messageContainer);
        });
    };
});