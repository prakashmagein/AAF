define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'mage/url',
    'Magento_Customer/js/model/customer'
], function ($, wrapper, quote, urlBuilder, customer) {
    'use strict';

    return function (selectShippingMethodAction) {
        return wrapper.wrap(selectShippingMethodAction, function (originalSelectShippingMethodAction, shippingMethod) {

            originalSelectShippingMethodAction(shippingMethod);

            // if (shippingMethod === null || window.location.hash !== "#shipping") {
            //     return;
            // }

            if (window.shippingGa4 !== undefined) {
                if (window.shippingGa4 === shippingMethod['carrier_title']) {
                    return;
                }
            }

            //Check complete form to send event add_shipping_infor :> trash-solution
            let shippingAddressSelected = $(".shipping-address-item.selected-item");
            if (shippingAddressSelected.length === 0) {
                let checkCompleteFormShipping = true,
                    fieldRequireds = $(".field._required");
                if (fieldRequireds.length !== 0) {
                    fieldRequireds.each(function (index, value) {
                        if (value.parentNode.id === "shipping-new-address-form" || value.parentNode.parentNode.parentNode.id === "shipping-new-address-form") {
                            if (value.style && value.style.display && value.style.display === "none") {
                                //No action, skip
                            } else {
                                let input = value.querySelector('input'),
                                    select = value.querySelector('select');
                                if ((input && input.value === "") || (select && select.value === "")) {
                                    checkCompleteFormShipping = false;
                                    return false;
                                }
                            }
                        }
                    })
                } else {
                    checkCompleteFormShipping = false;
                }
                let checkEmail = true;
                if (!customer.isLoggedIn()) {
                    let email = $('#customer-email-fieldset')[0];
                    if (email) {
                        let emailInput = email.querySelector('input'),
                            emailValue = emailInput.value,
                            email_regex = /^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i;
                        checkEmail = email_regex.test(emailValue);
                    }
                }

                if (!checkCompleteFormShipping || !checkEmail) {
                    return;
                }
            }
            //End check
	if (shippingMethod && shippingMethod.carrier_title) {
            $.ajax({
                url: urlBuilder.build('datalayerevents/select/shipping'),
                data: {
                    method: shippingMethod['carrier_title']
                },
                type: 'post',
                dataType: "json",
                cache: false,
                success: function (res) {
                    let element = $('.gwl_add_shipping_info');
                    if (element.length > 0 ) {
                        element.remove();
                    }
                    if (typeof(res.output) != "undefined"){
                        $('#checkout-step-shipping_method').append(res.output);
                        window.shippingGa4 = shippingMethod['carrier_title'];
                    }
                },
                error: function (res) {
                    console.log('send data event add shipping info fail');
                }
            });
            }
        });
    };

});
