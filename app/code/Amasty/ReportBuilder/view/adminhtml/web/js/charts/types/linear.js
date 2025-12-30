define([
    'amcharts'
], function (amcharts) {
    'use strict';

    var model = {
        /**
         * Axis Series initialization
         *
         * @private
         * @returns {void}
         */
        _initSeries: function () {
            if (this.chart.series.length) {
                this.chart.series.removeIndex(0);
            }

            this.series = new window.am4charts.LineSeries();
            this.chart.cursor.snapToSeries = this.series;
        },

        /**
         * Axis initialization
         *
         * @private
         * @param {String} axis
         * @param {String} type
         * @returns {void}
         */
        _initAxis: function (axis, type) {
            if (this.chart[axis + 'Axes'].length) {
                this.chart[axis + 'Axes'].removeIndex(0);
            }

            switch (type) {
                case 'smallint':
                case 'decimal':
                    this.initTypeValue(axis);
                    break;
                case 'text':
                case 'varchar':
                case 'int':
                    this.initTypeCategory(axis);
                    break;
                default:
                    this.initTypeDate(axis);
                    this.chart.cursor[axis + 'Axis'] = this['valueAxis' + axis.toUpperCase()];
            }
        },

        /**
         * Chart rendering
         *
         * @private
         * @returns {void}
         */
        _renderChart: function () {
            var self = this,
                labelX,
                labelY;

            self.chart.xAxes.push(self.valueAxisX);
            self.chart.yAxes.push(self.valueAxisY);
            self.chart.series.push(self.series);

            labelX = self.valueAxisX.renderer.labels.template;
            labelX.maxWidth = 150;
            labelX.truncate = true;
            labelX.wrap = true;
            labelX.tooltipText = '{valueX}';

            labelY = self.valueAxisY.renderer.labels.template;
            labelY.maxWidth = 200;
            labelY.wrap = true;
            labelY.tooltipText = '{valueY}';
        }
    };

    return {
        /**
         * @public
         * @param {Object} node
         * @returns {void}
         */
        init: function (node) {
            this.chart = window.am4core.create(node, amcharts.XYChart);

            this.initCursor('XYCursor');
            this.initScrollbar();
        },

        /**
         * @public
         * @param {Object} response
         * @returns {void}
         */
        update: function (response) {
            model._initSeries.call(this);
            model._initAxis.call(this, 'x', response.xAxisType);
            model._initAxis.call(this, 'y', response.yAxisType);
            model._renderChart.call(this);
        }
    };
});
