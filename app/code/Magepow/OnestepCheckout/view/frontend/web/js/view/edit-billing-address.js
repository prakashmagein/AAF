/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define([
    'ko',
    'Magento_Customer/js/model/address-list'
], function (ko, addressList) {
    'use strict';

    return function (address) {
        addressList().some(function (currentAddress, index, addresses) {
            if (currentAddress.getKey() === address.getKey()) {
                addressList.replace(currentAddress, address);
            }
        });

        addressList.valueHasMutated();

        return address;
    };
});
