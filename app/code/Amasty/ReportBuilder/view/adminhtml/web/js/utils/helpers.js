/**
 * Reports Builder helpers
 */

define([
    'jquery',
    'ko',
    'underscore'
], function ($, ko, _) {
    'use strict';

    /**
     * Update ko subscribe method with silent possibilities
     *
     * @param {Object} value
     * @returns {void}
     */
    ko.observable.fn.silentUpdate = function (value) {
        this.notifySubscribers = function () {};

        this(value);

        this.notifySubscribers = function () {
            ko.subscribable.fn.notifySubscribers.apply(this, arguments);
        };
    };

    return {
        /**
         * Components Array initialization and setting in target component
         *
         * @param {Array} array target uiClasses
         * @param {Object} component current uiClass
         * @returns {void}
         */
        initComponentsArray: function (array, component) {
            _.each(array, function (item) {
                component[_.isUndefined(item.uniq_name) ? item.index : item.uniq_name] = item;
            });
        },

        /**
         * Generate a 'uniq' string
         *
         * @returns {String}
         */
        getRandomString: function () {
            return Math.random().toString(36).substr(2, 9);
        },

        /**
         * Returns an element from main component in case that the element aren't loaded from resolver
         *
         * @public
         * @param {String} name
         * @param {Object} context
         * @returns {Object} - uiComponent
         */
        resolveComponent: function (name, context) {
            return _.isUndefined(context[name]) ? context.containers[0][name] : context[name];
        },

        /**
         * @param {Array} array
         * @returns {Boolean}
         */
        hasDuplicates: function (array) {
            return array.some(function (item) {
                return array.indexOf(item) !== array.lastIndexOf(item);
            });
        },

        /**
         * Parse chart xml config and return collection of objects, each object is one chart setting
         *
         * @private
         * @param {Object} settings - chart config from xml
         * @param {String} [prefix]
         * @returns {Object} - Collection of objects with path:value pair
         */
        _parseChartSettings: function (settings, prefix) {
            var self = this,
                keys = _.keys(settings),
                settingItem;

            // eslint-disable-next-line no-param-reassign
            prefix = prefix ? prefix + '.' : '';

            return keys.reduce(function (result, key) {
                if (_.isObject(settings[key])) {
                    // eslint-disable-next-line no-param-reassign
                    result = result.concat(self._parseChartSettings(settings[key], prefix + key));
                } else {
                    settingItem = {};
                    settingItem[prefix + key] = settings[key];

                    result.push(settingItem);
                }

                return result;
            }, []);
        }
    };
});
