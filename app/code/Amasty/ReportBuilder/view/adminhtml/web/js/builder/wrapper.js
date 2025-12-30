define([
    'underscore',
    'jquery',
    'uiComponent',
    'ko',
    'amrepbuilder_helpers',
    'uiRegistry'
], function (_, $, Component, ko, helpers, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_ReportBuilder/builder/wrapper',
            columnHeaderTmpl: 'Amasty_ReportBuilder/builder/header',
            columnTmpl: 'Amasty_ReportBuilder/builder/available_options/columns/column',
            components: [
                'index = chosen_options',
                'index = entities_list',
                'index = is_use_period',
                'index = amasty_report_builder_popup',
                'index = chart_picker'
            ]
        },

        /**
         * Init observable variables
         *
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe({
                    isVisible: false,
                    isOverlay: false
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

            this._super();

            registry.get(self.components, function () {
                helpers.initComponentsArray(arguments, self);

                self._usePeriodInit();
                self._chartFieldsInit();

                self.isVisible(!_.isUndefined(self.entitiesList.elems()) && !_.isEmpty(self.source.data.name));

                self.entitiesList.elems.subscribe(function (value) {
                    if (value) {
                        self.isVisible(!!value.length);
                    }
                });
            });
        },

        /**
         * Axis select initialization
         *
         * @returns {void}
         */
        _chartFieldsInit: function () {
            var self = this;

            self.entitiesList.currentPrimaryColumn.subscribe(function (item) {
                self.chartPicker.chartFieldsProcess().remove(item.id);
                self.chartPicker.chartFieldsProcess().add(item);
            });
        },

        /**
         * Use Period Select initialization
         *
         * @returns {void}
         */
        _usePeriodInit: function () {
            var self = this,
                nextColumn;

            self.isUsePeriod.visible(self.isUsePeriod.loaded);
            self.isUsePeriod.value(self.source.data.is_use_period);

            // eslint-disable-next-line consistent-return
            self.isUsePeriod.value.subscribe(function (value) {
                nextColumn = value
                    ? self.entitiesList.mainEntity.periodColumn()
                    : self.entitiesList.mainEntity.primaryColumn();

                if (!self.chosenOptions._checkColumnFiltrationStatus(self.entitiesList.currentPrimaryColumn())) {
                    self.entitiesList.setPrimaryColumn(nextColumn);

                    return false;
                }

                self.popup.open({
                    header: 'Are you sure?',
                    description: 'Current sorting and filters configuration of the main column will be lost.',
                    confirmCallback: function () {
                        self.entitiesList.setPrimaryColumn(nextColumn);
                        self.source.data.is_use_period = value;
                    },
                    cancelCallback: function () {
                        self.isUsePeriod.value(!self.isUsePeriod.value());
                        self.isUsePeriod.checked(self.isUsePeriod.value());
                    },
                    type: 'prompt'
                });
            });
        }
    });
});
