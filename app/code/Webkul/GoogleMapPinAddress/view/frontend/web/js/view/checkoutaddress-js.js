/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleMapPinAddress
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

define([
    'jquery',
    'uiComponent',
    'ko',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/checkout-data',
    'Webkul_GoogleMapPinAddress/js/view/shippingMap-js',
    'Webkul_GoogleMapPinAddress/js/model/map-config-provider',
    'Magento_Checkout/js/model/payment-service'
], function($, Component, ko, $t, alert, modal, mageCheck, shippingMap, mapData, paymentData) {
    'use strict';
    var i = 1;
    var markerBilling = '';
    var selectedMethod = '';
    var timer = '';
    var countryId = '';
    var countryName = '';
    var postalCode = '';
    var stateName = '';
    var addressData = '';
    var mapDataValue = mapData.getMapData();
    var defaultLatitude = mapDataValue['default_latitude'];
    var defaultLongitude = mapDataValue['default_longitude'];
    var paymentMethodList = '';
    return Component.extend({
        initCustomEvents: function() {
            paymentMethodList = paymentData.getAvailablePaymentMethods();
            selectedMethod = mageCheck.getSelectedPaymentMethod();
            if (selectedMethod == null) {
                selectedMethod = paymentMethodList[0]['method'];
            }
            var self = this;
            $(".billing-address-same-as-shipping-block").click(function(event) {
                if ($(this).find("input[type = checkbox]").is(":checked") == false) {
                    selectedMethod = mageCheck.getSelectedPaymentMethod();
                    if (selectedMethod == null) {
                        selectedMethod = paymentMethodList[0]['method'];
                    }
                    var longitudeDivName = "billingAddress" + selectedMethod + ".custom_attributes.longitude";
                    var linkUrl = window.location.href;
                    var res = linkUrl.match(/payment/g);
                    if (res !== null && res[0] == 'payment') {
                        $("div[name = '" + longitudeDivName + "']").append($(".mapContainerBilling"));
                        $(".mapContainerBilling").show();
                        if (mapDataValue['status'] != '0') {
                            if ($(".mapContainerBilling").length) {
                                if (!$(".billing-address-form .fieldset.address .choice.field").length) {
                                    $("div[name = '" + longitudeDivName + "']").css({ 'margin-bottom': '200px' });
                                }
                            }
                        }
                    }
                }
            });

            $('.opc-progress-bar-item').click(function(events) {
                var linkUrl = window.location.href;
                var res = linkUrl.match(/shipping/g);
                if (res[0] == 'shipping') {
                    $(".mapContainerBilling").hide();
                    if (i) {
                        shippingMap().onElementRender();
                        i = 0;
                    }
                }
            });

            timer = setTimeout(function() {
                if ($(document).find(".billing-address-details .action.action-edit-address").length) {
                    $(".billing-address-details .action.action-edit-address").click(function() {
                        selectedMethod = mageCheck.getSelectedPaymentMethod();
                        if (selectedMethod == null) {
                            selectedMethod = paymentMethodList[0]['method'];
                        }
                        var longitudeDivName = "billingAddress" + selectedMethod + ".custom_attributes.longitude";
                        var linkUrl = window.location.href;
                        var res = linkUrl.match(/payment/g);
                        if (res !== null && res[0] == 'payment') {
                            $("div[name = '" + longitudeDivName + "']").append($(".mapContainerBilling"));
                            $(".mapContainerBilling").show();
                            if (!$(".billing-address-form .fieldset.address .choice.field").length) {
                                $("div[name = '" + longitudeDivName + "']").css({ 'margin-bottom': '200px' });
                            }
                        };
                    });
                    clearTimeout(timer);
                }
            }, 500);
            timer = setTimeout(function() {
                if ($(document).find(".payment-method._active").length) {
                    $(".payment-method").click(function() {
                        selectedMethod = mageCheck.getSelectedPaymentMethod();
                        if (selectedMethod == null) {
                            selectedMethod = paymentMethodList[0]['method'];
                        }
                        var longitudeDivName = "billingAddress" + selectedMethod + ".custom_attributes.longitude";
                        var linkUrl = window.location.href;
                        var res = linkUrl.match(/payment/g);
                        if (res !== null && res[0] == 'payment') {
                            $("div[name = '" + longitudeDivName + "']").append($(".mapContainerBilling"));
                            $(".mapContainerBilling").show();
                            if (!$(".billing-address-form .fieldset.address .choice.field").length) {
                                $("div[name = '" + longitudeDivName + "']").css({ 'margin-bottom': '200px' });
                            }
                        };
                    });
                    selectedMethod = mageCheck.getSelectedPaymentMethod();
                    if (selectedMethod == null) {
                        selectedMethod = paymentMethodList[0]['method'];
                    }
                    var longitudeDivName = "billingAddress" + selectedMethod + ".custom_attributes.longitude";
                    var linkUrl = window.location.href;
                    var res = linkUrl.match(/payment/g);
                    if (res !== null && res[0] == 'payment') {
                        $("div[name = '" + longitudeDivName + "']").append($(".mapContainerBilling"));
                        $(".mapContainerBilling").show();
                        if (!$(".billing-address-form .fieldset.address .choice.field").length) {
                            $("div[name = '" + longitudeDivName + "']").css({ 'margin-bottom': '200px' });
                        }
                    };
                    clearTimeout(timer);
                }
            }, 500);
        },
        _create: function() {},
        afterElementRenderForCheckout: function() {
            var self = this;
            if (mapDataValue['status'] != false) {
                if (mapDataValue['api_key'] != null && mapDataValue['api_key'] != "") {
                    self.initCustomEvents();
                    selectedMethod = mageCheck.getSelectedPaymentMethod();
                    if (selectedMethod == null) {
                        selectedMethod = paymentMethodList[0]['method'];
                    }
                    var longitudeDivName = "billingAddress" + selectedMethod + ".custom_attributes.longitude";
                    var latiDivName = "billingAddress" + selectedMethod + ".custom_attributes.latitude";
                    var billLongitude = $("div[name = '" + longitudeDivName + "'] input[name = 'custom_attributes[longitude]']").val();
                    var billLatitude = $("div[name = '" + latiDivName + "'] input[name = 'custom_attributes[latitude]']").val();
                    $("div[name = '" + longitudeDivName + "'] input[name = 'custom_attributes[longitude]']").val(defaultLongitude);
                    $("div[name = '" + latiDivName + "'] input[name = 'custom_attributes[latitude]']").val(defaultLatitude);
                    var myLatLng = { lat: billLatitude ? parseFloat(billLatitude) : parseFloat(defaultLatitude), lng: billLongitude ? parseFloat(billLongitude) : parseFloat(defaultLongitude) };
                    var mapBilling = new google.maps.Map(document.getElementById('mapbilling'), {
                        center: myLatLng,
                        zoom: 8
                    });
                    markerBilling = new google.maps.Marker({
                        position: myLatLng,
                        map: mapBilling,
                        title: 'PinDrop',
                        draggable: true
                    });
                    $("div[name = '" + longitudeDivName + "'] input[name = 'custom_attributes[longitude]']").val(defaultLongitude);
                    $("div[name = '" + longitudeDivName + "'] input[name = 'custom_attributes[longitude]']").trigger('keyup');
                    $("div[name = '" + latiDivName + "'] input[name = 'custom_attributes[latitude]']").val(defaultLatitude);
                    $("div[name = '" + latiDivName + "'] input[name = 'custom_attributes[latitude]']").trigger('keyup');
                    google.maps.event.addListener(markerBilling, 'dragend', function(event) {
                        var latitude = this.getPosition().lat();
                        var longitude = this.getPosition().lng();
                        var latLng = { lat: latitude, lng: longitude };
                        var longitudeDivName = "billingAddress" + selectedMethod + ".custom_attributes.longitude";
                        var latiDivName = "billingAddress" + selectedMethod + ".custom_attributes.latitude";
                        $("div[name = '" + longitudeDivName + "'] input[name = 'custom_attributes[longitude]']").val(longitude);
                        $("div[name = '" + longitudeDivName + "'] input[name = 'custom_attributes[longitude]']").trigger('keyup');
                        $("div[name = '" + latiDivName + "'] input[name = 'custom_attributes[latitude]']").val(latitude);
                        $("div[name = '" + latiDivName + "'] input[name = 'custom_attributes[latitude]']").trigger('keyup');
                        geoCoderLocationGate(latLng);
                    });

                    function geoCoderLocationGate(latLng) {
                        var geocoder = new google.maps.Geocoder();
                        var streetAddress = '';
                        geocoder.geocode({
                            'latLng': latLng
                        }, function(results, status) {
                            if (status ==
                                google.maps.GeocoderStatus.OK) {
                                if (results[0]) {
                                    var addrComp = results[0].address_components;
                                    for (var i = addrComp.length - 1; i >= 0; i--) {
                                        if (addrComp[i].types[0] == "country") {
                                            var countryDivName = "billingAddress" + selectedMethod + ".country_id";
                                            var country = addrComp[i].short_name;
                                            $("div[name = '" + countryDivName + "'] select[name='country_id'] option[value='" + country + "']").attr("selected", true);
                                            $("div[name = '" + countryDivName + "'] select[name='country_id']").trigger('change');
                                        } else if (addrComp[i].types[0] == "administrative_area_level_1") {
                                            var state = addrComp[i].long_name;
                                            var stateDivName = "billingAddress" + selectedMethod + ".region_id"
                                            if ($("div[name = '" + stateDivName + "'] select[name = 'region_id']").is(':visible')) {
                                                $('div[name = "' + stateDivName + '"] select[name = "region_id"] option:contains("' + state + '")').attr("selected", true);
                                                $('input[name = region]').attr("value", '');
                                                $('div[name = "' + stateDivName + '"] select[name = "region_id"]').trigger('change');
                                            } else {
                                                var stateDivName = "billingAddress" + selectedMethod + ".region";
                                                $('div[name = "' + stateDivName + '"] input[name = region]').val(state);
                                                $('div[name = "' + stateDivName + '"] input[name = region]').trigger('keyup');
                                            }
                                        } else if (addrComp[i].types[0] == "administrative_area_level_2") {
                                            var city = addrComp[i].long_name;
                                            var cityDivName = "billingAddress" + selectedMethod + ".city";
                                            $('div[name = "' + cityDivName + '"] input[name="city"]').val(city);
                                            $('div[name = "' + cityDivName + '"] input[name="city"]').trigger('keyup');
                                        } else if (addrComp[i].types[0] == "postal_code") {
                                            var postal = addrComp[i].long_name;
                                            var postalDivName = "billingAddress" + selectedMethod + ".postcode";
                                            $('div[name = "' + postalDivName + '"] input[name="postcode"]').val(postal);
                                            $('div[name = "' + postalDivName + '"] input[name="postcode"]').trigger('keyup');
                                        } else if (addrComp[i].types[0] == 'street_number') {
                                            streetAddress = addrComp[i].long_name + ", " + streetAddress;
                                        } else if (addrComp[i].types[0] == 'route') {
                                            streetAddress = addrComp[i].long_name + ", " + streetAddress;
                                        } else if (addrComp[i].types[0] == 'neighborhood') {
                                            streetAddress = addrComp[i].long_name + ", " + streetAddress;
                                        } else if (addrComp[i].types[0] == 'sublocality_level_3') {
                                            streetAddress = addrComp[i].long_name + ", " + streetAddress;
                                        } else if (addrComp[i].types[0] == 'sublocality_level_2') {
                                            streetAddress = addrComp[i].long_name + ", " + streetAddress;
                                        } else if (addrComp[i].types[0] == 'sublocality_level_1') {
                                            streetAddress = addrComp[i].long_name + ", " + streetAddress;
                                        } else if (addrComp[i].types[0] == 'locality') {
                                            streetAddress = addrComp[i].long_name + ", " + streetAddress;
                                        }
                                    }
                                    if (streetAddress) {
                                        streetAddress = streetAddress.trim();
                                        streetAddress = streetAddress.substring(0, streetAddress.length - 1);
                                        var streetDivName = "billingAddress" + selectedMethod + ".street.0";
                                        $("div[name = '" + streetDivName + "'] input[name = 'street[0]']").val(streetAddress)
                                        $("div[name = '" + streetDivName + "'] input[name = 'street[0]']").trigger('keyup');
                                    }
                                } else {
                                    alert({ content: $t("No results found.") });
                                }
                            }
                        });

                    }

                    function geoCoderLocationGatebyCustomAddress(addressData) {
                        var geocoder = new google.maps.Geocoder();
                        geocoder.geocode({
                            'address': addressData
                        }, function(results, status) {
                            if (status ==
                                google.maps.GeocoderStatus.OK) {
                                if (results[0]) {
                                    var addrLatitude = results[0].geometry.location.lat();
                                    var addrLongitude = results[0].geometry.location.lng();
                                    var latLangByAddress = { lat: addrLatitude, lng: addrLongitude };
                                    var longitudeDivName = "billingAddress" + selectedMethod + ".custom_attributes.longitude";
                                    var latiDivName = "billingAddress" + selectedMethod + ".custom_attributes.latitude";
                                    $("div[name = '" + longitudeDivName + "'] input[name = 'custom_attributes[longitude]']").val(addrLongitude);
                                    $("div[name = '" + longitudeDivName + "'] input[name = 'custom_attributes[longitude]']").trigger('keyup');
                                    $("div[name = '" + latiDivName + "'] input[name = 'custom_attributes[latitude]']").val(addrLatitude);
                                    $("div[name = '" + latiDivName + "'] input[name = 'custom_attributes[latitude]']").trigger('keyup');
                                    markerBilling.setPosition(latLangByAddress);
                                    mapBilling.setCenter(latLangByAddress);
                                    geoCoderLocationGate(latLangByAddress);
                                } else {
                                    alert({ content: $t("No results found.") });
                                }
                            }
                        });
                    }

                    function loadEvents() {
                        $(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id']").focusout(function() {
                            countryId = $(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id']").val();
                        countryName = $(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id'] option[value='" + countryId + "']").text();
                        stateName = $(document).find("div[name ='billingAddress" + selectedMethod + ".region_id'] select[name='region_id'] option:selected").text();
                        postalCode = $(document).find("div[name ='billingAddress" + selectedMethod + ".postcode'] input[name='postcode']").val();
                        
                        if (countryName && postalCode && stateName) {
                            addressData = stateName + " " + postalCode + ", " + countryName;
                            getAddressBilling(addressData);
                        }
                        });
                        $(document).find("div[name ='billingAddress" + selectedMethod + ".region_id'] select[name = 'region_id']").focusout(function() {
                            countryId = $(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id']").val();
                        countryName = $(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id'] option[value='" + countryId + "']").text();
                        stateName = $(document).find("div[name ='billingAddress" + selectedMethod + ".region_id'] select[name='region_id'] option:selected").text();
                        postalCode = $(document).find("div[name ='billingAddress" + selectedMethod + ".postcode'] input[name='postcode']").val();
                            
                        if (countryName && postalCode && stateName) {
                            addressData = stateName + " " + postalCode + ", " + countryName;
                            getAddressBilling(addressData);
                        }
                        });
                        $(document).find("div[name ='billingAddress" + selectedMethod + ".region'] input[name = 'region']").focusout(function() {
                            stateName = $(document).find("div[name ='billingAddress" + selectedMethod + ".region'] input[name='region']").val();

                            if (countryName && postalCode && stateName) {
                                addressData = stateName + " " + postalCode + ", " + countryName;
                                getAddressBilling(addressData);
                            }
                        });
                        $(document).find("div[name ='billingAddress" + selectedMethod + ".postcode'] input[name = 'postcode']").focusout(function() {
                            countryId = $(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id']").val();
                        countryName = $(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id'] option[value='" + countryId + "']").text();
                        stateName = $(document).find("div[name ='billingAddress" + selectedMethod + ".region_id'] select[name='region_id'] option:selected").text();
                        postalCode = $(document).find("div[name ='billingAddress" + selectedMethod + ".postcode'] input[name='postcode']").val();
                        
                        if (countryName && postalCode && stateName) {
                            addressData = stateName + " " + postalCode + ", " + countryName;
                            getAddressBilling(addressData);
                        }
                        });
                    }
                    timer = setTimeout(function() {
                        if ($(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id']").length) {
                            loadEvents();
                            clearTimeout(timer);
                        };
                    }, 500);
                    $(document).on('click', '.edit-address-link, .new-address-popup .action-show-popup', function() {
                        loadEvents();
                    });

                    $(document).on('focusout', "div[name ='billingAddress" + selectedMethod + ".region_id'] select[name = 'region_id']", function() {
                        countryId = $(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id']").val();
                        countryName = $(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id'] option[value='" + countryId + "']").text();
                        stateName = $(document).find("div[name ='billingAddress" + selectedMethod + ".region_id'] select[name='region_id'] option:selected").text();
                        postalCode = $(document).find("div[name ='billingAddress" + selectedMethod + ".postcode'] input[name='postcode']").val();
                        
                        if (countryName && postalCode && stateName) {
                            addressData = stateName + " " + postalCode + ", " + countryName;
                            geoCoderLocationGatebyCustomAddress(addressData);
                        }
                    });

                    $(document).on('focusout', "div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id']", function() {
                        countryId = $(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id']").val();
                        countryName = $(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id'] option[value='" + countryId + "']").text();
                        stateName = $(document).find("div[name ='billingAddress" + selectedMethod + ".region_id'] select[name='region_id'] option:selected").text();
                        postalCode = $(document).find("div[name ='billingAddress" + selectedMethod + ".postcode'] input[name='postcode']").val();
                        
                        getAddressBilling(countryName);

                    });
                    $(document).on('focusout', "div[name ='billingAddress" + selectedMethod + ".postcode'] input[name = 'postcode']", function() {
                        countryId = $(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id']").val();
                        countryName = $(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id'] option[value='" + countryId + "']").text();
                        stateName = $(document).find("div[name ='billingAddress" + selectedMethod + ".region_id'] select[name='region_id'] option:selected").text();
                        postalCode = $(document).find("div[name ='billingAddress" + selectedMethod + ".postcode'] input[name='postcode']").val();
                        
                        if (countryName && postalCode && stateName) {
                            addressData = stateName + " " + postalCode + ", " + countryName;
                            geoCoderLocationGatebyCustomAddress(addressData);
                        }
                    });
                    if ($(document).find("div[name ='billingAddress" + selectedMethod + ".country_id'] select[name='country_id']").length) {
                        loadEvents();
                    }
                    function getAddressBilling(addressData) {
                        geoCoderLocationGatebyCustomAddress(addressData);
                        countryId = '';
                        countryName = '';
                        postalCode = '';
                        stateName = '';
                        addressData = '';
                    }
                }
            }
        }
    });
});