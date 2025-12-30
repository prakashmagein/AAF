define([
    'jquery',
    'underscore',
    'uiComponent',
    'ko',
    'amcharts',
    'amrepbuilder_helpers',
    'uiRegistry',
    'amrepbuilder_charts_collection'
], function ($, _, Component, ko, amcharts, helpers, registry, chartsCollection) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_ReportBuilder/components/chart',
            chart: {
                data: {}
            },
            interval: 'day',
            components: [
                'index = bookmarks',
                'index = amrepbuilder_loader'
            ],
            tooltipText: '{%1}: [bold]{%2}[/]'
        },

        /**
         * @public
         * @param {Object} node
         * @returns {Boolean | Object}
         */
        initChart: function (node) {
            if (!this.source.display_chart) {
                return false;
            }

            this._setChartModel(this.source.chart_type);
            this._initComponents();
            this._initChartModel(node);
            this._setChartTheme(window.am4themes_animated);
            this._initExport();

            return this;
        },

        /**
         * @public
         * @returns {void}
         */
        initLegend: function () {
            this.chart.legend = new window.am4charts.Legend();
        },

        /**
         * @public
         * @param {String} type
         * @returns {void}
         */
        initCursor: function (type) {
            this.chart.cursor = new window.am4charts[type]();
        },

        /**
         * @public
         * @returns {void}
         */
        initScrollbar: function () {
            this.chart.scrollbarX = new window.am4core.Scrollbar();

            this.chart.zoomOutButton.marginRight = 60;
        },

        /**
         * Axis Type Value initialization
         *
         * @private
         * @param {string} axis target axis name
         * @returns {void}
         */
        initTypeValue: function (axis) {
            var axisUpperCase = axis.toUpperCase();

            this['valueAxis' + axisUpperCase] = new window.am4charts.ValueAxis();
            this['valueAxis' + axisUpperCase].dataFields['value' + axisUpperCase] = 'value' + axisUpperCase;
            this.series.dataFields['value' + axisUpperCase] = 'value' + axisUpperCase;

            this['valueAxis' + axisUpperCase].min = 0;
        },

        /**
         * Axis Type Category initialization
         *
         * @private
         * @param {string} axis target axis name
         * @returns {void}
         */
        initTypeCategory: function (axis) {
            var axisUpperCase = axis.toUpperCase();

            this.category = 'value' + axisUpperCase;

            this['valueAxis' + axisUpperCase] = new window.am4charts.CategoryAxis();
            this['valueAxis' + axisUpperCase].dataFields.category = 'value' + axisUpperCase;
            this.series.dataFields['category' + axisUpperCase] = 'value' + axisUpperCase;
        },

        /**
         * Axis Type Date initialization
         *
         * @private
         * @param {string} axis target axis name
         * @returns {void}
         */
        initTypeDate: function (axis) {
            var self = this,
                axisUpperCase = axis.toUpperCase();

            self.chart.dateFormatter.firstDayOfWeek = 0;

            self['valueAxis' + axisUpperCase] = new window.am4charts.DateAxis();
            self.series.dataFields['date' + axisUpperCase] = 'value' + axisUpperCase;
            self['valueAxis' + axisUpperCase].baseInterval = {
                'timeUnit': self.interval,
                'count': 1
            };

            self.chart.dateFormatter.inputDateFormat = self.source.datePattern;
        },

        /**
         * Sorting values by series
         *
         * @public
         * @param {Object} axis - valueAxisX or valueAxisY
         * @returns {void}
         */
        sortBySeries: function (axis) {
            axis.sortBySeries = this.series;
        },

        /**
         * Set labels offset
         *
         * @public
         * @param {Object} axis - valueAxisX or valueAxisY
         * @param {Array} data
         * @returns {void}
         */
        setLabelsOffsetX: function (axis, data) {
            if (data.length > 10) {
                axis.renderer.labels.template.adapter.add('dy', this._calculateLabelOverflow());
            }
        },

        /**
         * @public
         * @param {String} firstValue
         * @param {String} secondValue
         * @returns {String}
         */
        getTooltipText: function (firstValue, secondValue) {
            return this.tooltipText.replace('%1', firstValue).replace('%2', secondValue);
        },

        /**
         * Set all settings from config to the amChart object
         *
         * @public
         * @param {Object} config - chart config from xml
         * @returns {void}
         */
        setChartConfig: function (config) {
            _.each(config, function (item) {
                this._setChartProperty(item);
            }.bind(this));
        },

        /**
         * Add an offset for labels to prevent overflow
         *
         * @private
         * @returns {Function} - callback
         */
        _calculateLabelOverflow: function () {
            return function (axis, target) {
                // eslint-disable-next-line no-bitwise,no-self-compare
                if (target.dataItem && target.dataItem.index & 2 === 2) {
                    return axis + 25;
                }

                return axis;
            };
        },

        /**
         * Set amChart property.
         *
         * @private
         * @param {Object} item
         * @returns {void}
         */
        _setChartProperty: function (item) {
            var object = this,
                path,
                i;

            path = item.path.split('.');

            for (i = 0; i < path.length - 1; i++) {
                // eslint-disable-next-line no-param-reassign
                object = object[path[i]];
            }

            object[path[i]] = this._validateSettingType(item.value, item.type);
        },

        /**
         * Validator contains methods to validate amChart setting type
         *
         * @desc am4core types: 'percent', 'color'
         *
         * @private
         * @param {String} value
         * @param {String} type - property type
         * @returns {String | Number | Object}
         */
        _validateSettingType: function (value, type) {
            if (type === 'percent' || type === 'color') {
                return window.am4core[type](value);
            }

            return type === 'number' ? Number(value) : value;
        },

        /**
         * @private
         * @returns {void}
         */
        _initComponents: function () {
            var self = this;

            registry.get(self.components, function () {
                helpers.initComponentsArray(arguments, self);

                self.bookmarks.on('saveState', function () {
                    self._updateData();
                });
            });
        },

        /**
         * @private
         * @param {String} type - chart type
         * @returns {void}
         */
        _setChartModel: function (type) {
            this.chartType = type;
            this[this.chartType] = chartsCollection[this.chartType];
        },

        /**
         * @private
         * @param {Object} node
         * @returns {Object}
         */
        _initChartModel: function (node) {
            this[this.chartType].init.call(this, node);
        },

        /**
         * @private
         * @param {Object} response
         * @returns {Object}
         */
        _updateChart: function (response) {
            this[this.chartType].update.call(this, response);
        },

        /**
         * @private
         * @param {Function} theme
         * @returns {void}
         */
        _setChartTheme: function (theme) {
            window.am4core.useTheme(theme);
        },

        /**
         * Update chart Data via Ajax
         *
         * @private
         * @return {Object}
         */
        _updateData: _.debounce(function () {
            var self = this;

            $.ajax({
                url: self.source.chart_update_url,
                data: {
                    report_id: self.source.report_id,
                    grid_data: self.bookmarks.current
                },
                method: 'POST',
                showLoader: false
            }).done(function (response) {
                self.chart.data = response.data;
                self.interval = response.interval;

                self._updateChart(response);

                self.loader.isLoad(false);
            });
        }),

        /**
         * Chart data exporting initialization
         *
         * @private
         * @returns {void}
         */
        _initExport: function () {
            var self = this;

            self.chart.exporting.menu = new window.am4core.ExportMenu();
            self.chart.exporting.menu.items = [{
                label: '...',
                menu: [{
                    label: 'Image',
                    menu: [
                        { type: 'png', label: 'PNG' },
                        { type: 'jpg', label: 'JPG' },
                        { type: 'svg', label: 'SVG' },
                        { type: 'pdf', label: 'PDF' }
                    ]
                }, {
                    label: 'Print', type: 'print'
                }]
            }];
        }
    });
});
