define([
    'jquery',
    'underscore',
    'uiComponent',
    'ko',
    'amrepbuilder_helpers',
    'uiRegistry',
    'rjsResolver'
], function ($, _, Component, ko, helpers, registry, resolver) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_ReportBuilder/builder/chosen_options/wrapper',
            templates: {
                column: 'Amasty_ReportBuilder/builder/chosen_options/columns/column',
                columnsList: 'Amasty_ReportBuilder/builder/chosen_options/columns/columns_list',
                button: 'Amasty_ReportBuilder/components/button',
                header: 'Amasty_ReportBuilder/builder/header',
                columnHeader: 'Amasty_ReportBuilder/builder/chosen_options/columns/header',
                columnFooter: 'Amasty_ReportBuilder/builder/chosen_options/columns/footer',
                overlay: 'Amasty_ReportBuilder/builder/chosen_options/overlay',
                howToLink: 'Amasty_ReportBuilder/builder/howto_link'
            },
            title: 'Chosen options',
            descr: 'Add the columns here that you would like to configure to be displayed on the report page',
            elems: ko.observableArray([]).extend({ deferred: true }),
            entitiesList: {},
            components: [
                'index = entities_list',
                'index = amreportbuilder_report_form',
                'index = chosen_column_header',
                'index = component_search',
                'index = chosen_column_toolbar',
                'index = amasty_report_builder',
                'index = amasty_report_builder_popup'
            ],
            extendComponents: null,
            imports: {
                howto_link: '${ $.provider }:howto_link'
            }
        },
        selectors: {
            column: '[data-amrepbuilder-js="column"]:not(.-disabled)'
        },
        classes: {
            hover: '-hovered',
            disabled: '-disabled'
        },

        /**
         * Init observable variables
         *
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe({
                    isEdited: false,
                    isOverlay: false,
                    dndZone: null
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

            self._extendComponentsList();

            registry.get(self.components, function () {
                helpers.initComponentsArray(arguments, self);

                self._initPrimaryColumn();
            });

            resolver(function () {
                if (self.source.data && self.source.data.chosen_data) {
                    self._initChosenData();
                }
            });
        },

        /**
         * Extend the array of components names
         *
         * @returns {void}
         */
        _extendComponentsList: function () {
            if (this.extendComponents) {
                this.components = this.components.concat(this.extendComponents);
            }
        },

        /**
         * Initialize DropZone
         *
         * @param {Object} element
         * @returns {void}
         */
        initDropZone: function (element) {
            var self = this;

            self.dropZone = $(element);
            self.dropZone.droppable({
                over: function () {
                    self.dropZone.addClass(self.classes.hover);
                },
                out: function () {
                    self.dropZone.removeClass(self.classes.hover);
                },
                drop: function (event, ui) {
                    self.currentDropItem = ui.draggable;
                    self.dropZone.removeClass(self.classes.hover);
                }
            });
        },

        /**
         * Initialize Drag and Drop functionality for target column
         *
         * @param {Object} item prototype column node element
         * @returns {void}
         */
        initDnD: function (item) {
            var self = this;

            self.setDnDZone(item);

            $(item).sortable({
                items: self.selectors.column,
                receive: self.receiveCallbackDnD.bind(self),
                start: function (event, ui) {
                    self.isSorting = true;
                    ui.placeholder.css('visibility', 'visible');
                    self.currentSortingItemIndex = ui.item.index();
                },
                stop: function (event, ui) {
                    if (self.isSorting) {
                        self._sortColumn(ui);
                    }
                }
            });
        },

        /**
         * Store dndZone to the observable variable
         *
         * @param {Object} item prototype column node element
         * @returns {void}
         */
        setDnDZone: function (item) {
            this.dndZone($(item));
        },

        /**
         * Drag and Drop receive callback
         *
         * @param {Object} event Jquery.event
         * @param {Object} ui
         * @returns {void}
         */
        receiveCallbackDnD: function (event, ui) {
            var self = this,
                prototype = $(ui.item).data('item');

            self.addColumn(prototype, self.currentDropItem.index());
            self.currentDropItem.remove();
        },

        /**
         * Initialize particular column
         *
         * @param {Object} column target column
         * @returns {void}
         */
        initColumn: function (column) {
            this.columnHeader.initItem(column);
            column.isFooterActive = ko.observable(false);
            this.toolbar.initAggregation(column);
            this.toolbar.initFiltration(column);
            column.isDate = ko.observable(column.isDate || false);
            column.isVisible = ko.observable(
                typeof column.isVisible !== 'undefined' ? column.isVisible : true
            );

            if (!ko.isObservable(column.sortStatus)) {
                column.sortStatus = ko.observable(column.sortStatus || 0);
            }
        },

        /**
         * Reset sorting in all chosen columns
         * @returns {void}
         */
        resetSorting: function () {
            this.elems.each(function (column) {
                column.sortStatus(0);
            });
        },

        /**
         *  Add target column from available list to chosen list
         *
         *  @param {Object} prototype target prototype
         *  @param {Number} index target position
         *  @returns {void}
         */
        addColumn: function (prototype, index) {
            var self = this,
                prototypeEntity = self.entitiesList.elems()[prototype.entity_index];

            // eslint-disable-next-line no-param-reassign
            index = index || self.elems().length;

            prototypeEntity.searchCount(prototypeEntity.searchCount() - 1);
            self.search.clearColumnTitle(prototype);
            self.initColumn(prototype);
            prototype.isDisabled(true);
            prototypeEntity.chosenColumnsList.push(prototype.index);

            if (self.elems().length === index) {
                self.elems.push(prototype);
            } else {
                self.elems.splice(index, 0, prototype);
            }

            self.builder.chartPicker.chartFieldsProcess().add(prototype);
            self.isSorting = false;
        },

        /**
         * Remove columns list from chosen index
         *
         * @param {Number} targetIndex
         * @returns {void}
         */
        removeColumns: function (targetIndex) {
            var self = this,
                index = 0;

            self.elems.each(function () {
                if (index > targetIndex) {
                    self.removeColumn(self.elems()[index--]);
                }

                index++;
            });
        },

        /**
         * Remove target column from chosen list
         *
         * @param {Object} item target item
         * @returns {void}
         */
        removeColumn: function (item) {
            if (item.sortStatus()) {
                this.entitiesList.currentPrimaryColumn().sortStatus(2);
            }

            this.builder.chartPicker.chartFieldsProcess().remove(item.id);

            if (!_.isUndefined(item.entity_index) && !_.isUndefined(item.entity_index)) {
                this.entitiesList.removeChosenColumn(item.entity_index, item.index);
                this.entitiesList.clearColumn(item.entity_index, item.name);
            }

            this.elems.remove(function (column) {
                return column.id === item.id;
            });
        },

        /**
         * Clearing all elems from chosen list and enabling prototypes elements
         * @returns {void}
         */
        clearAll: function () {
            var self = this;

            self.popup.open({
                header: 'Are you sure?',
                description: 'Do you really want to delete all columns?',
                confirmCallback: function () {
                    self.removeColumns(0);
                },
                type: 'prompt'
            });
        },

        /**
         *  Sorting columns Method
         *
         *  @param {Object} ui DnD helper class
         *  @returns {void}
         */
        _sortColumn: function (ui) {
            var self = this,
                targetIndex = ui.item.index(),
                prototype;

            if (targetIndex !== this.currentSortingItemIndex) {
                prototype = self.elems.splice(self.currentSortingItemIndex, 1)[0];

                self.elems.splice(targetIndex, 0, prototype);
            }
        },

        /**
         * Chosen Data initialization
         * @returns {void}
         */
        _initChosenData: function () {
            var self = this,
                entity,
                prototype;

            _.each(self.source.data.chosen_data, function (column) {
                entity = self.entitiesList.getEntityByName(column.entity_name);
                prototype = {};

                // TODO: (https://git.amasty.com/magento2/module-report-builder/-/merge_requests/316#note_197452)
                if (entity) {
                    column.entity_index = entity.index;
                }

                self.initColumn(column);

                if (entity) {
                    prototype = entity.columns()[column.name];
                } else {
                    prototype = column;
                }

                if (prototype) {
                    if (entity) {
                        entity.chosenColumnsList.push(prototype.index);
                    }

                    prototype.position = column.position;
                    prototype.isDate = ko.observable(column.isDate());
                    prototype.isFilter = ko.observable(column.isFilter());
                    prototype.isVisible = ko.observable(column.isVisible());
                    prototype.sortStatus = ko.observable(column.sortStatus());

                    if (prototype.isDisabled) {
                        column.isDisabled = prototype.isDisabled;
                        column.isDisabled(true);
                    } else {
                        column.isDisabled = ko.observable(true);
                    }
                }
            });

            self.entitiesList.currentPrimaryColumn.silentUpdate(self.source.data.chosen_data[0]);
            self.elems(self.source.data.chosen_data);
        },

        /**
         * Initialize primary column
         * @returns {void}
         */
        _initPrimaryColumn: function () {
            var self = this;

            self.entitiesList.currentPrimaryColumn.subscribe(function (column) {
                if (self.elems()[0]) {
                    self.removeColumn(self.elems()[0]);
                }

                self.elems.unshift(column);
            });
        },

        /**
         * Checking column filtration status
         *
         * @param {Object} column
         * @returns {Boolean}
         */
        _checkColumnFiltrationStatus: function (column) {
            if (column.position === undefined) {
                return false;
            }

            return column.isDate() || !column.isVisible() || column.isFilter() || column.sortStatus();
        }
    });
});
