/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define(['ko'], function (ko) {
    'use strict';
    return {
        isReviewRequired: ko.observable(false),
        customerEmail: ko.observable(null),
        active: ko.observable(false)
    }
});
