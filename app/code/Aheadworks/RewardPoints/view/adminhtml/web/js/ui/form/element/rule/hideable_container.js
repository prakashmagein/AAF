define([
    'uiCollection',
    'underscore'
], function (Collection, _) {
    'use strict';

    return Collection.extend({
        defaults: {},

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe({
                    'visible': true
                });

            return this;
        },

        /**
         * Show element.
         *
         * @returns {Object} Chainable.
         */
        show: function () {
            this.visible(true);

            return this;
        },

        /**
         * Hide element.
         *
         * @returns {Object} Chainable.
         */
        hide: function () {
            this.visible(false);

            return this;
        }
    });
});
