/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleMapPinAddress
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define(
    ['ko'],
    function(ko) {
        'use strict';
        var mapData = (typeof window.checkoutConfig === "undefined") ? {} : window.checkoutConfig.map;
        return {
            mapData: mapData,
            getMapData: function() {
                return mapData;
            }
        };
    }
);