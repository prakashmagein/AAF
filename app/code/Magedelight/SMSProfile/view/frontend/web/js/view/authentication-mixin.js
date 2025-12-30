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
    'Magedelight_SMSProfile/js/utils'
], function ($,intlTelInput,utils) {
    'use strict';
    var mixin = {
        MageMobileRenderCompletePopup: function () {
            if (window.checkoutConfig.customer_country_enabled==1) {
                var phoneValidation = window.checkoutConfig.phone_validation;
                var phoneValidationArray = [];
                if (phoneValidation) {
                    var json = $.parseJSON(phoneValidation);
                    $(json).each(function (i, val) {
                        phoneValidationArray[val.country.toLowerCase()] = val.digit.toLowerCase();
                    });
                }
            
                var custreginput = document.querySelector(".block-customer-login #login_mobile");
                var custregiti = window.intlTelInput(custreginput, {
                    nationalMode:true,
                    separateDialCode: true,
                    utilsScript: utils,
                    formatOnDisplay: false,
                    preferredCountries:window.checkoutConfig.preferred_countries,
                    onlyCountries:window.checkoutConfig.only_countries
                    });

                var element=document.querySelector(".block-customer-login #countryreg");
                if (document.contains(element)) {
                    document.querySelector(".block-customer-login #countryreg").value = '+'+custregiti.getSelectedCountryData().dialCode;
                    document.querySelector(".block-customer-login #countryregcode").value = custregiti.getSelectedCountryData().iso2;

                    //add validation class in mobile input
                    var countryCode = $(".block-customer-login #countryregcode").val();
                    var className = "required-entry input-text popup validate-length validate-digits";
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

                    $('.block-customer-login #login_mobile').removeClass();
                    $('.block-customer-login #login_mobile').addClass(className);
                }

                custreginput.addEventListener('countrychange', function (e) {
                    document.querySelector(".block-customer-login #countryreg").value = '+'+custregiti.getSelectedCountryData().dialCode;
                    document.querySelector(".block-customer-login #countryregcode").value = custregiti.getSelectedCountryData().iso2;
                
                    var className = "required-entry input-text popup validate-length validate-digits";
                    var countryCode = $(".block-customer-login #countryregcode").val();

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

                    $('.block-customer-login #login_mobile').removeClass();
                    $('.block-customer-login #login_mobile').addClass(className);

                });

                $(".iti__flag-container").hide();
            } else {
                var className = "required-entry input-text popup validate-length validate-digits";
                className += " maximum-length-"+window.checkoutConfig.mobile_default_validation;
                className += " minimum-length-"+window.checkoutConfig.mobile_default_validation;
                $('.block-customer-login #login_mobile').removeClass();
                $('.block-customer-login #login_mobile').addClass(className);
            }
        }
    };

    return function (target) {
 // target == Result that Magento_Ui/.../columns returns.
        return target.extend(mixin); // new result that all other modules receive
    };
    
});
