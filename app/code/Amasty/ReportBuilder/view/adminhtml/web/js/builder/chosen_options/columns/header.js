/**
 * Column Header Component
 */

define([
    'jquery',
    'underscore',
    'uiComponent',
    'ko',
    'amrepbuilder_helpers',
    'uiRegistry'
], function ($, _, Component, ko, helpers, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_ReportBuilder/builder/chosen_options/columns/header',
            templates: {
                button: 'Amasty_ReportBuilder/components/button',
                checkIcon: 'Amasty_ReportBuilder/components/icons/check_mark',
                editIcon: 'Amasty_ReportBuilder/components/icons/edit',
                closeIcon: 'Amasty_ReportBuilder/components/icons/close'
            },
            components: [
                'index = chosen_options',
                'index = chart_picker'
            ]
        },
        classes: {
            edit: '-edit'
        },

        /**
         * Init observable variables
         *
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe({
                    editableItem: null,
                    currentValue: null
                });

            return this;
        },

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         *
         * @returns {void}
         */
        initialize: function () {
            var self = this;

            self._super();

            registry.get(self.components, function () {
                helpers.initComponentsArray(arguments, self);
            });
        },

        /**
         * Item states initialization
         *
         * @param {Object} item
         * @returns {void | Boolean}
         */ // eslint-disable-next-line consistent-return
        initItem: function (item) {
            if (ko.isObservable(item.customTitle)) {
                return false;
            }

            item.customTitle = ko.observable(item.customTitle || false);
            item.isEdited = ko.observable(false);
        },

        /**
         * On focus item title event Method
         *
         * @param {Object} item
         * @returns {void}
         */
        onFocus: function (item) {
            item.isEdited(true);
            this.chosenOptions.isEdited(true);
            this.editableItem(item);
            this.currentValue(item.customTitle());

            if (!item.customTitle()) {
                item.customTitle(item.title);
            }
        },

        /**
         * On Blur event Component Method
         *
         * @returns {void}
         */
        onBlur: function () {
            if (this.editableItem()) {
                this.editableItem().isEdited(false);
                this.currentValue(null);
            }

            this.chosenOptions.isEdited(false);
            this.editableItem(null);
        },

        /**
         * Save item title Method
         *
         * @returns {void}
         */
        save: function () {
            var editableItem = this.editableItem();

            if (editableItem.title === editableItem.customTitle() || !editableItem.customTitle()) {
                this.reset();
            }

            if (!_.isNull(this.currentValue()) && !_.isNull(editableItem) && this._valueNotEqual(editableItem)) {
                this.chartPicker.chartFieldsProcess().rename(editableItem);
            }

            this.onBlur();
        },

        /**
         * @param {Object} editableItem
         * @returns {Boolean}
         */
        _valueNotEqual: function (editableItem) {
            return this.currentValue() !== editableItem.customTitle();
        },

        /**
         * Reset item title to default Method
         *
         * @returns {void}
         */
        reset: function () {
            this.editableItem().customTitle('');
            this.onBlur();
        },

        /**
         * Cancelling editing Method
         *
         * @returns {void}
         */
        cancel: function () {
            this.editableItem().customTitle(this.currentValue());
            this.onBlur();
        }
    });
});
