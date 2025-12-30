/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define([
	    'underscore',
	    'ko',
	    'mageUtils',
	    'uiComponent',
	    'uiLayout',
	    'Magento_Customer/js/model/address-list'
	], function (_, ko, utils, Component, layout, addressList) {
	    'use strict';
	    return Component.extend({
		defaults: {
		    template: 'Magepow_OnestepCheckout/address/billing/list',
		    visible: addressList().length > 0,
		    rendererTemplates: []
		},

		/** @inheritdoc */
		initialize: function () {
		    this._super()
		        .initChildren();

		    addressList.subscribe(function (changes) {
		            var self = this;

		            changes.forEach(function (change) {
		                if (change.status === 'added') {
		                    self.createRendererComponent(change.value, change.index);
		                }
		            });
		        },
		        this,
		        'arrayChange'
		    );

		    return this;
		},

		/** @inheritdoc */
		initConfig: function () {
		    this._super();
		    // the list of child components that are responsible for address rendering
		    this.rendererComponents = [];

		    return this;
		},

		/** @inheritdoc */
		initChildren: function () {
		    _.each(addressList(), this.createRendererComponent, this);

		    return this;
		},

		createRendererComponent: function (address, index) {
		    var rendererTemplate, templateData, rendererComponent;

		    if (index in this.rendererComponents) {
		        this.rendererComponents[index].address(address);
		    } else {
		        // rendererTemplates are provided via layout
		        rendererTemplate = address.getType() != undefined && this.rendererTemplates[address.getType()] != undefined ? //eslint-disable-line
		            utils.extend({}, defaultRendererTemplate, this.rendererTemplates[address.getType()]) :
		            defaultRendererTemplate;
		        templateData = {
		            parentName: this.name,
		            name: index
		        };
		        rendererComponent = utils.template(rendererTemplate, templateData);
		        utils.extend(rendererComponent, {
		            address: ko.observable(address)
		        });
		        layout([rendererComponent]);
		        this.rendererComponents[index] = rendererComponent;
		    }
		}
	    });
	});
