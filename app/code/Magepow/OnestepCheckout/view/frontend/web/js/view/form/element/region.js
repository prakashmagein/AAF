/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */

define([
    'underscore',
    'Magento_Ui/js/form/element/region',
    'mageUtils',
    'uiLayout'
], function (_, Component, utils, layout) {
    'use strict';
    var template = window.checkoutConfig.mageConfig.isUsedMaterialDesign ? 'Magepow_OnestepCheckout/form/field' : '${ $.$data.template }';
    var inputNode = {
        parent: '${ $.$data.parentName }',
        component: 'Magento_Ui/js/form/element/abstract',
        template: template,
        elementTmpl: 'Magepow_OnestepCheckout/form/element/input',
        provider: '${ $.$data.provider }',
        name: '${ $.$data.index }_input',
        dataScope: '${ $.$data.customEntry }',
        customScope: '${ $.$data.customScope }',
        sortOrder: '${ $.$data.sortOrder }',
        displayArea: 'body',
        label: '${ $.$data.label }'
    };

    return Component.extend({
        initInput: function () {
            layout([utils.template(_.extend(inputNode, {additionalClasses: this.additionalClasses}), this)]);

            return this;
        }
    });
});

