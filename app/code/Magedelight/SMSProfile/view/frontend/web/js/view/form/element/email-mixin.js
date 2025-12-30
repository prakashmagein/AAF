/**
 * Magedelight
 * Copyright (C) 2023 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2023 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */

define([
    'jquery',
    'intlTelInput',
    'Magedelight_SMSProfile/js/utils',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Customer/js/action/login'
], function ($,intlTelInput,utils,fullScreenLoader,loginAction) {
    'use strict';
    var mixin = {
        MageMobileRenderComplete: function () {
            if (window.checkoutConfig.customer_country_enabled==1) {
                var phoneValidation = window.checkoutConfig.phone_validation;
                var phoneValidationArray = [];
                if (phoneValidation) {
                    var json = $.parseJSON(phoneValidation);
                    $(json).each(function (i, val) {
                        phoneValidationArray[val.country.toLowerCase()] = val.digit.toLowerCase();
                    });
                }
            
                var custreginput = document.querySelector("#customer-email-fieldset #login_mobile");
                var custregiti = window.intlTelInput(custreginput, {
                    nationalMode:true,
                    separateDialCode: true,
                    utilsScript: utils,
                    formatOnDisplay: false,
                    preferredCountries:window.checkoutConfig.preferred_countries,
                    onlyCountries:window.checkoutConfig.only_countries
                });

                var element=document.querySelector("#customer-email-fieldset #countryreg");
                if (document.contains(element)) {
                    document.querySelector("#customer-email-fieldset #countryreg").value = '+'+custregiti.getSelectedCountryData().dialCode;
                    document.querySelector("#customer-email-fieldset #countryregcode").value = custregiti.getSelectedCountryData().iso2;

                    //add validation class in mobile input
                    var countryCode = $("#customer-email-fieldset #countryregcode").val();
                    var className = "input-text popup validate-length validate-digits required-entry";
                    if (phoneValidationArray[countryCode]) {
                        className += " maximum-length-"+phoneValidationArray[countryCode];
                        className += " minimum-length-"+phoneValidationArray[countryCode];
                        length = phoneValidationArray[countryCode];
                    } else if (phoneValidationArray['default']) {
                        className += " maximum-length-"+phoneValidationArray['default'];
                        className += " minimum-length-"+phoneValidationArray['default'];
                        length = phoneValidationArray['default'];
                    } else {
                        className += " maximum-length-10";
                        className += " minimum-length-10";
                        length = 10;
                    }

                    $('#customer-email-fieldset #login_mobile').removeClass();
                    $('#customer-email-fieldset #login_mobile').addClass(className);
                }

                custreginput.addEventListener('countrychange', function (e) {
                    document.querySelector("#customer-email-fieldset #countryreg").value = '+'+custregiti.getSelectedCountryData().dialCode;
                    document.querySelector("#customer-email-fieldset #countryregcode").value = custregiti.getSelectedCountryData().iso2;
                
                    var className = "input-text popup validate-length validate-digits required-entry";
                    var countryCode = $("#customer-email-fieldset #countryregcode").val();

                    if (phoneValidationArray[countryCode]) {
                        className += " maximum-length-"+phoneValidationArray[countryCode];
                        className += " minimum-length-"+phoneValidationArray[countryCode];
                        length = phoneValidationArray[countryCode];
                    } else if (phoneValidationArray['default']) {
                        className += " maximum-length-"+phoneValidationArray['default'];
                        className += " minimum-length-"+phoneValidationArray['default'];
                        length = phoneValidationArray['default'];
                    } else {
                        className += " maximum-length-10";
                        className += " minimum-length-10";
                        length = 10;
                    }

                    $('#customer-email-fieldset #login_mobile').removeClass();
                    $('#customer-email-fieldset #login_mobile').addClass(className);

                });

                $(".iti__flag-container").hide();
            } else {
                var className = "required-entry input-text popup validate-length validate-digits";
                className += " maximum-length-"+window.checkoutConfig.mobile_default_validation;
                className += " minimum-length-"+window.checkoutConfig.mobile_default_validation;
                $('#customer-email-fieldset #login_mobile').removeClass();
                $('#customer-email-fieldset #login_mobile').addClass(className);
            }

            $("#customer-email-fieldset .block-customer-login").hide();
        },

        emailHasChanged: function () {
            if (window.checkoutConfig.enable_on_checkoutpage==1) {
                return true;
            }

            if (window.checkoutConfig.enable_on_checkoutpage==0) {
                this._super();
            }
        },

        login: function (loginForm) {
            var loginData = {},
                formDataArray = $(loginForm).serializeArray();

            formDataArray.forEach(function (entry) {
                loginData[entry.name] = entry.value;
            });

            if (window.checkoutConfig.enable_on_checkoutpage==1) {
                if ($(loginForm).validation() && $(loginForm).validation('isValid')) {
                    fullScreenLoader.startLoader();
                    loginAction(loginData).always(function () {
                        fullScreenLoader.stopLoader();
                    });
                }
            } else {
                if (this.isPasswordVisible() && $(loginForm).validation() && $(loginForm).validation('isValid')) {
                    fullScreenLoader.startLoader();
                    loginAction(loginData).always(function () {
                        fullScreenLoader.stopLoader();
                    });
                }
            }
        },
    };

    return function (target) {
        return target.extend(mixin);
    };
    
});
