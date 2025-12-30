define([
    'jquery',
    'moment'
], function ($, moment) {
    'use strict';

    return function (validator) {
        validator.addRule(
            'custom-validate-telephone',
            function (value, params) {
                var phoneno = /^[0-9]+$/;
                if (typeof value === 'string') {
                if((value.match(phoneno))){
                    return true;
                }else{
                    return false;
                }
                }else{
                    return false;
                }                       
            },
            $.mage.__("Please enter the numeric value only.")
        );

        return validator;
    };
});