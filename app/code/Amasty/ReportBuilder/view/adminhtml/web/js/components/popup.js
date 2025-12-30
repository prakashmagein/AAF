/**
 *  Amasty Popup UI Component
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'underscore'
], function ($, ko, Component, _) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_ReportBuilder/components/popup'
        },
        classes: {
            openPopup: '-popup-opened'
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super()
                .observe({
                    isActive: false,
                    header: '',
                    description: '',
                    checkbox: false,
                    confirmCallback: false,
                    cancelCallback: false,
                    content: false,
                    buttons: [],
                    type: false
                });

            return this;
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            var self = this;

            self._super();

            self.type.subscribe(function (value) {
                if (value === 'prompt') {
                    self._initPrompt();
                }

                if (value === 'alert') {
                    self._initAlert();
                }
            });
        },

        /**
         * Popup Wrapper Container Init
         *
         * @param {Object} node
         * @returns {void}
         */
        initWrapper: function (node) {
            var self = this;

            self.wrapper = $(node);
            node.click(function (event) {
                if (node.is(event.target)) {
                    self.close();
                }
            });
        },

        /**
         * Show method
         *
         * @param {Object} data popup
         * @returns {void}
         */
        open: function (data) {
            var self = this;

            $('body').addClass(self.classes.openPopup);

            _.each(data, function (value, index) {
                self[index](value);
            });

            self.isActive(true);
        },

        /**
         * Hide method
         *
         * @param {Boolean} [confirm]
         * @returns {void}
         */
        close: function (confirm) {
            var self = this;

            if (!_.isBoolean(confirm) && self.cancelCallback()) {
                self.cancelCallback()();
            }

            self.isActive(false);
            self._clear();
            $('body').removeClass(self.classes.openPopup);
        },

        /**
         * Clear method
         *
         * @returns {void}
         */
        _clear: function () {
            var self = this;

            self.header(false);
            self.cancelCallback(false);
            self.confirmCallback(false);
            self.content(false);
            self.description(false);
            self.checkbox(false);
            self.buttons([]);
            self.type(false);
        },

        /**
         * Popup type prompt Init
         *
         * @returns {void}
         */
        _initPrompt: function () {
            var self = this;

            self.buttons([
                {
                    text: 'Cancel',
                    classes: '-clear -link -cancel',
                    callback: function () {
                        self.close();
                    }
                },
                {
                    text: 'Yes',
                    classes: '-primary',
                    callback: function () {
                        if (self.confirmCallback()) {
                            self.confirmCallback()();
                        }

                        self.close(true);
                    }
                }
            ]);
        },

        /**
         * Popup type alert Init
         *
         * @returns {void}
         */
        _initAlert: function () {
            var self = this;

            self.buttons([
                {
                    text: 'Ok',
                    classes: '-primary',
                    callback: function () {
                        self.close();
                    }
                }
            ]);
        }
    });
});
