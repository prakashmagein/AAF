define([
    'ko',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'amrepbuilder_helpers',
    'mage/translate',
    'uiRegistry',
    'amrepbuilder_charts_collection'
], function (ko, _, Component, helpers, $t, registry, chartsCollection) {
    'use strict';

    return Component.extend({
        defaults: {
            components: [
                'index = amasty_report_builder_popup'
            ],
            links: {
                chosenOptionsData: 'index = chart_fields_options:options',
                displayChartChecked: 'index = display_chart:checked'
            },
            templates: {
                chartIconsPath: 'Amasty_ReportBuilder/components/icons/charts/'
            },
            messages: {
                sameFields: $t('Fields values cannot be the same'),
                chooseAllFields: $t('Please select all chart fields')
            },
            chartsDescription: chartsCollection.description,
            chart: {
                id: ko.observable(''),
                chart_type: ko.observable('linear'),
                axises: ko.observableArray([])
            }
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            var self = this;

            this._super();

            registry.get(self.components, function () {
                helpers.initComponentsArray(arguments, self);
            });

            return this;
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            var self = this;

            this._super()
                .observe({
                    isValid: false,
                    chartHasDuplicates: false,
                    displayChartChecked: false,
                    chartData: [],
                    chosenOptionsData: []
                });

            self._setChartFieldsData(self.chart.chart_type());

            self.chart.chart_type.subscribe(function (currentValue) {
                self._setChartFieldsData(currentValue);
                self._setChartPickerValue();
            });

            return this;
        },

        /**
         * @inheritDoc
         */
        getInitialValue: function () {
            var data;

            if (this._hasChartData() && this.displayChartChecked()) {
                data = ko.toJS(this.source.data.chart);

                this.chart.chart_type(data.chart_type);
                this.chart.id(data.id);

                _.each(this.chart.axises(), function (item, i) {
                    _.extend(item, data.axises[i]);
                });

                return ko.toJSON(data);
            }

            return ko.toJSON(this.chart);
        },

        /**
         * @public
         * @returns {void}
         */
        clearChartData: function () {
            this.value('');
            this.source.data.chart = '';
        },

        /**
         * @param {Event} event - input select event
         * @public
         * @returns {void}
         */
        inputSelectListener: function (event) {
            var self = this;

            if (_.isUndefined(event) || _.isUndefined(event.target.value)) {
                return;
            }

            _.delay(function () {
                self._checkDuplicateValues();
                self._setChartPickerValue();
                self._showError(self.chartHasDuplicates() ? self.messages.sameFields : false);
            }, 0);
        },

        /**
         * Triggers on form saving. Validates Chart picker component and show popup and error messages
         *
         * @public
         * @returns {Boolean}
         */
        // eslint-disable-next-line consistent-return
        validateChartPicker: function () {
            var message;

            if (this._isChartPickerValid()) {
                return true;
            }

            message = this.chartHasDuplicates() ? this.messages.sameFields : this.messages.chooseAllFields;

            this._showError(message);

            this.popup.open({
                header: 'Oops!',
                description: message,
                type: 'alert'
            });

            return false;
        },

        /**
         * Methods to manipulates the chart fields
         *
         * @public
         * @returns {Object} - methods
         */
        chartFieldsProcess: function () {
            var self = this;

            return {
                /**
                 * Adding target select option for fields
                 *
                 * @public
                 * @param {Object} item
                 * @returns {void}
                 */
                add: function (item) {
                    var values = self.chart.axises().map(function (element) {
                        return _.clone(element);
                    });

                    self.chosenOptionsData.push(self.chartFieldsProcess().createOption(item));

                    self.chart.axises(values);
                },

                /**
                 * Removing target select option for fields by option id
                 *
                 * @public
                 * @param {String} id
                 * @returns {void}
                 */
                remove: function (id) {
                    var values = self.chart.axises().map(function (element) {
                        if (element.value === id) {
                            delete element.value;
                        }

                        return _.clone(element);
                    });

                    self.chosenOptionsData.remove(function (item) {
                        return item.value === id;
                    });

                    self.chart.axises(values);
                },

                /**
                 * Rename target select option for fields by option id
                 *
                 * @public
                 * @param {Object} editableItem
                 * @returns {void}
                 */
                rename: function (editableItem) {
                    var values = self.chart.axises().map(function (element) {
                        return _.clone(element);
                    });

                    _.find(self.chosenOptionsData(), function (element) {
                        if (element.value === editableItem.id) {
                            element.label = editableItem.customTitle();
                            element.labeltitle = editableItem.customTitle();
                        }
                    });

                    self.chart.axises(values);
                },

                /**
                 * Clearing fields data
                 *
                 * @public
                 * @returns {void}
                 */
                clear: function () {
                    self.chosenOptionsData.splice(0);
                },

                /**
                 * Creating axis select option from item prototype
                 *
                 * @public
                 * @param {Object} item
                 * @returns {Object} created option
                 */
                createOption: function (item) {
                    return {
                        label: item.title(),
                        labeltitle: item.title(),
                        value: item.id
                    };
                }
            };
        },

        /**
         * @param {Object} item
         * @public
         * @returns {Boolean}
         */
        chartItemUnavailable: function (item) {
            return _.isUndefined(item.axises);
        },

        /**
         * @private
         * @returns {Boolean}
         */
        _hasChartData: function () {
            return !_.isUndefined(this.source.data.chart);
        },

        /**
         * @private
         * @returns {void}
         */
        _setChartPickerValue: function () {
            this.value(ko.toJSON(this.chart));
        },

        /**
         * Search values for duplicates in the array of chart axises
         *
         * @private
         * @returns {void}
         */
        _checkDuplicateValues: function () {
            var result = [];

            // eslint-disable-next-line array-callback-return
            this.chart.axises().map(function (item) {
                var value = item.value;

                if (value) {
                    result.push(value);
                }
            });

            this.chartHasDuplicates(helpers.hasDuplicates(result));
        },

        /**
         * @private
         * @param {String} message
         * @returns {void}
         */
        _showError: function (message) {
            this.error(message);
            this.error.valueHasMutated();
            this.bubble('error', message);
        },

        /**
         * @private
         * @returns {Boolean}
         */
        _isChartPickerValid: function () {
            if (this.chartHasDuplicates()) {
                return false;
            }

            return _.isUndefined(_.find(this.chart.axises(), function (item) {
                return !('value' in item) || _.isUndefined(item.value);
            }));
        },

        /**
         * @private
         * @param {String} type
         * @returns {void}
         */
        _setChartFieldsData: function (type) {
            this.chart.axises(this._getChartByValue(type).axises);
        },

        /**
         * @private
         * @param {String} value
         * @returns {Object}
         */
        _getChartByValue: function (value) {
            return _.find(this.chartsDescription, function (element) {
                return element.value === value;
            });
        }
    });
});
