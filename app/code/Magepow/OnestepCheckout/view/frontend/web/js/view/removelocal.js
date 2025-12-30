define([
    'jquery'
], function ($){
    'use strict';
    $.widget('mage.removelocal', {

        _init: function () {
            window.localStorage.removeItem('mage-cache-storage');    
        }
    });
    return $.mage.removelocal;
});