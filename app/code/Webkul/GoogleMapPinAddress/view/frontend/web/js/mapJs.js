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
    "jquery",
    "mage/translate",
    "Magento_Ui/js/modal/alert",
    "jquery/ui"
], function($, $t, alert) {
    "use strict";
    var countryId = '';
    var countryName = '';
    var postalCode = '';
    var stateName = '';
    var addressData = '';
    var cityName = '';
    $.widget('googlemappinaddress.mapjs', {
        _create: function() {
            var apiKey = this.options.ApiKey;
            var defualtLatitude = this.options.DefualtLatitude;
            var defualtLongitude = this.options.DefualtLongitude;
            var currentLatLong = getCustomerCurrentLatLong();
            if (currentLatLong) {
                defualtLatitude = currentLatLong.lat;
                defualtLongitude = currentLatLong.lng;
            }

            function getCustomerCurrentLatLong() {
                if (navigator.geolocation) {
                    return navigator.geolocation.getCurrentPosition(getGeo, gererror);
                } else {
                    console.log("Browser doesn't support geolocation!");
                    return false;
                }
            }

            function getGeo(position) {
                var pos = { lat: position.coords.latitude, lng: position.coords.longitude };
                return pos;
            }

            function gererror(error) {
                console.log(error);
                return false;
            }
            if (apiKey != '') {
                // google.maps.event.addDomListener(window, 'load', initAutocomplete);
                var field = '';
                var postal = $('#zip').val();
                var city = $('#city').val();
                var countryCode = $('#country').val();
                var country = $("#country option[value='" + countryCode + "']").text();
                var state = $('#region').val();
                // var geocoder = new google.maps.Geocoder();
                var latitude = $('#latitude').val();
                var longitude = $('#longitude').val();
                var latLng = { lat: latitude ? parseFloat(latitude) : '', lng: longitude ? parseFloat(longitude) : '' };
                var address = city + "," + country;
                if (latLng.lat != "") {
                    address = null;
                } else {
                    latLng = '';
                }
                var myLatLng = { lat: latitude ? parseFloat(latitude) : parseFloat(defualtLatitude), lng: longitude ? parseFloat(longitude) : parseFloat(defualtLongitude) };
                if (city != "") {
                    geoCoderLocationGatebyAddress(address);
                } else {
                    geoCoderLocationGate(myLatLng);
                }

                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 4,
                    center: myLatLng
                });
                var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                    title: 'PinDrop',
                    draggable: true
                });
                google.maps.event.addListener(marker, 'dragend', function(event) {
                    var latit = this.getPosition().lat();
                    var longi = this.getPosition().lng();
                    $('#latitude').val(latit);
                    $('#longitude').val(longi);
                    latLng = { lat: latit, lng: longi };
                    geoCoderLocationGate(latLng);
                });


                function geoCoderLocationGate(latLng) {
                    $('#latitude').val(latLng.lat);
                    $('#longitude').val(latLng.lng);
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        'latLng': latLng
                    }, function(results, status) {
                        if (status ==
                            google.maps.GeocoderStatus.OK) {
                            if (results[0]) {
                                var streetAddress = '';
                                var addrComp = results[0].address_components;
                                for (var i = addrComp.length - 1; i >= 0; i--) {
                                    if (addrComp[i].types[0] == "country") {
                                        country = addrComp[i].short_name;
                                        $('#country option[value="' + country + '"]').attr("selected", true);
                                        $('#country').trigger('change');
                                    } else if (addrComp[i].types[0] == "administrative_area_level_1") {
                                        state = addrComp[i].long_name;
                                        if ($('#region_id').is(':disabled')) {
                                            $('#region').val(state);
                                            $('#region').attr("value", state);
                                        } else {
                                            $('#region_id option:contains("' + state + '")').attr("selected", true);
                                            $('#region').attr("value", '');
                                            $('#region_id').trigger('change');
                                        }
                                    } else if (addrComp[i].types[0] == "administrative_area_level_2") {
                                        city = addrComp[i].long_name;
                                        $('#city').val(city);
                                    } else if (addrComp[i].types[0] == "postal_code") {
                                        postal = addrComp[i].long_name;
                                        $('#zip').val(postal);
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
                                if (streetAddress != '') {
                                    streetAddress = streetAddress.trim();
                                    streetAddress = streetAddress.substring(0, streetAddress.length - 1);
                                    $("#street_1").val(streetAddress);
                                }
                            } else {
                                alert({ content: $t("No results found.") });
                            }
                        }
                    });

                }

                function geoCoderLocationGatebyAddress(address) {
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        'address': address
                    }, function(results, status) {
                        if (status ==
                            google.maps.GeocoderStatus.OK) {
                            if (results[0]) {
                                var addrLatitude = results[0].geometry.location.lat();
                                var addrLongitude = results[0].geometry.location.lng();
                                var latLangByAddress = { lat: addrLatitude, lng: addrLongitude };
                                $('#latitude').val(addrLatitude);
                                $('#longitude').val(addrLongitude);
                                marker.setPosition(latLangByAddress);
                                map.setCenter(latLangByAddress);
                                geoCoderLocationGate(latLangByAddress);
                            } else {
                                alert({ content: $t("No results found.") });
                            }
                        }
                    });
                }

                function getAddress(address) {
                    geoCoderLocationGatebyAddress(address);
                    countryId = '';
                    countryName = '';
                    postalCode = '';
                    stateName = '';
                    addressData = '';
                    cityName = '';
                }
                $("#country").focusout(function() {
                    countryId = $(this).val();
                    countryName = $("#country option[value='" + countryId + "']").text();
                    if (countryName) {
                        addressData = countryName;
                        getAddress(addressData);
                    }
                });
                $("#region_id").focusout(function() {
                    stateName = $("#region_id option:selected").text();
                    countryName = $("#country option:selected").text();
                    if (countryName && stateName) {
                        addressData = stateName + ", " + countryName;
                        getAddress(addressData);
                    }
                });
                $('#city').focusout(function() {
                    cityName = $(this).val();
                    stateName = $("#region_id option:selected").text();
                    countryName = $("#country option:selected").text();
                    if (countryName && cityName) {
                        addressData = cityName + " " + stateName + ", " + countryName;
                        getAddress(addressData);
                    }
                });
                $('#region').focusout(function() {
                    stateName = $(this).val();
                    cityName = $('#city').val();
                    countryName = $("#country option:selected").text();
                    if (countryName && cityName) {
                        addressData = cityName + " " + stateName + ", " + countryName;
                        getAddress(addressData);
                    }
                });
                $('#zip').focusout(function() {
                    postalCode = $(this).val();
                    stateName = $(this).val();
                    countryName = $("#country option:selected").text();
                    if (countryName && postalCode && stateName) {
                        addressData = stateName + " " + postalCode + ", " + countryName;
                        getAddress(addressData);
                    }
                });
            }
        }
    });
    return $.googlemappinaddress.mapjs;
});