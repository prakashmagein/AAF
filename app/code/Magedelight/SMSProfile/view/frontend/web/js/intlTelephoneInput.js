define([
    'jquery',
    'intlTelInput'
], function ($) {
    var initIntl = function (config, node) {
        var custregitilogin = window.intlTelInput(node, {
            nationalMode : config.nationalMode,
            separateDialCode: config.separateDialCode,
            utilsScript : config.utilsScript,
            preferredCountries : config.preferredCountries,
            onlyCountries : config.onlyCountries
        });
        var element=document.querySelector("#countryreg");

        var phoneValidation = $("#phone_validation").val();
        var phoneValidationArray = [];
        if (phoneValidation) {
            var json = $.parseJSON(phoneValidation);
            $(json).each(function (i, val) {
                phoneValidationArray[val.country.toLowerCase()] = val.digit.toLowerCase();
            });
        }

        if (document.contains(element)) {
            document.querySelector("#countryreg").value = '+'+custregitilogin.getSelectedCountryData().dialCode;
            document.querySelector("#countryregcode").value = custregitilogin.getSelectedCountryData().iso2;
            
            $("#md-register-content #countryreg").val('+'+custregitilogin.getSelectedCountryData().dialCode);
            $("#md-register-content #countryregcode").val(custregitilogin.getSelectedCountryData().iso2);

            $("#md-forgot-content #countryreg").val('+'+custregitilogin.getSelectedCountryData().dialCode);
            $("#md-forgot-content #countryregcode").val(custregitilogin.getSelectedCountryData().iso2);

            //add validation class in mobile input
            var countryCode = $("#countryregcode").val();
            var className = "form-field__input input-text validate-length validate-digits";
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

            $('#login_mobile').removeClass();
            $('#login_mobile').addClass(className);
            $('#md-register-content #login_mobile').removeClass();
            $('#md-register-content #login_mobile').addClass(className);
            $('#md-forgot-content #login_mobile').removeClass();
            $('#md-forgot-content #login_mobile').addClass(className);

            if (document.contains(document.querySelector(".customer-address-form")) && custregitilogin.getSelectedCountryData().dialCode!=undefined) {
                node.value = custregitilogin.getSelectedCountryData().dialCode;
            }
        }
        node.addEventListener('countrychange', function (e) {
            
            $("#md-register-content #countryreg").val('+'+custregitilogin.getSelectedCountryData().dialCode);
            $("#md-register-content #countryregcode").val(custregitilogin.getSelectedCountryData().iso2);

            $("#md-forgot-content #countryreg").val('+'+custregitilogin.getSelectedCountryData().dialCode);
            $("#md-forgot-content #countryregcode").val(custregitilogin.getSelectedCountryData().iso2);

            document.querySelector("#countryreg").value = '+'+custregitilogin.getSelectedCountryData().dialCode;
            document.querySelector("#countryregcode").value = custregitilogin.getSelectedCountryData().iso2;

            //add validation class in mobile input
            var countryCode = $("#countryregcode").val();
            var className = "form-field__input input-text validate-length validate-digits";
            if (phoneValidationArray[countryCode]) {
                className += " maximum-length-"+phoneValidationArray[countryCode];
                className += " minimum-length-"+phoneValidationArray[countryCode];
                length = phoneValidationArray[countryCode];
            } else if (phoneValidationArray['default']) {
                className += " maximum-length-"+phoneValidationArray['default'];
                className += " minimum-length-"+phoneValidationArray['default'];
                length = phoneValidationArray['default'];
            } else {
                className += " maximum- -10";
                className += " minimum-length-10";
                length = 10;
            }

            $('#login_mobile').removeClass();
            $('#login_mobile').addClass(className);
            $('#md-register-content #login_mobile').removeClass();
            $('#md-register-content #login_mobile').addClass(className);
            $('#md-forgot-content #login_mobile').removeClass();
            $('#md-forgot-content #login_mobile').addClass(className);

            if (document.contains(document.querySelector(".customer-address-form"))) {
                node.value = custregitilogin.getSelectedCountryData().dialCode;
            }
        });

    };
    return initIntl;
});