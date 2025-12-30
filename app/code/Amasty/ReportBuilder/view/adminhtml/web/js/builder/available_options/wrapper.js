define([
    'jquery',
    'uiComponent',
    'ko',
    'underscore',
    'amrepbuilder_helpers',
    'uiRegistry',
    'jquery/ui'
], function ($, Component, ko, _, helpers, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            maxColumnQty: 8,
            columnHeaderTmpl: 'Amasty_ReportBuilder/builder/available_options/columns/header',
            columnFooterTmpl: 'Amasty_ReportBuilder/builder/available_options/columns/footer',
            template: 'Amasty_ReportBuilder/builder/available_options/entities_list/wrapper',
            entityTmpl: 'Amasty_ReportBuilder/builder/available_options/entities_list/entity',
            columnTmpl: 'Amasty_ReportBuilder/builder/available_options/columns/column',
            columnsListTmpl: 'Amasty_ReportBuilder/builder/available_options/columns/columns_list',
            buttonTmpl: 'Amasty_ReportBuilder/components/button',
            mainEntity: {
                entity_index: 0,
                entity_name: null,
                primaryColumn: ko.observable(),
                periodColumn: ko.observable()
            },
            links: {
                isSearched: 'index = component_search:isSearched'
            },
            components: [
                'index = amreportbuilder_report_form',
                'index = chosen_options',
                'index = amasty_report_builder',
                'index = chosen_column_toolbar',
                'index = is_use_period'
            ]
        },
        selectors: {
            chosenList: '[data-amrepbuilder-js="chosen-list"]',
            chosenBlock: '[data-amrepbuilder-js="chosen-block"]'
        },

        /**
         * Init observable variables
         *
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe({
                    isSearched: false,
                    currentPrimaryColumn: false,
                    isDragging: false
                });

            return this;
        },

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         * @returns {void}
         */
        initialize: function () {
            var self = this;

            self._super();

            self.elems = ko.observableArray([]).extend({ deferred: true });

            registry.get(self.components, function () {
                helpers.initComponentsArray(arguments, self);

                if (self.source.data.entities) {
                    self.initEntities(self.source.data.entities);
                }

                self.elems(self.source.data.entities);
            });
        },

        /**
         * Initialize particular entities list
         *
         * @param {Array} items target entities
         * @returns {void}
         */
        initEntities: function (items) {
            var self = this;

            _.each(items, function (item, index) {
                self._initEntity(item, index);
            });
        },

        /**
         * Initialize particular entity
         *
         * @param {Object} item target entity
         * @param {Number} index target entity index
         * @returns {void}
         */
        _initEntity: function (item, index) {
            item.index = index;
            item.isActive = ko.observable(false);
            item.searchCount = ko.observable(0);
            item.isViewAll = ko.observable(false);
            item.chosenColumnsList = ko.observableArray([]);
            item.columns = ko.observable(item.columns);

            this._initColumns(item);

            if (index === 0) {
                this._initMainEntity(item);
            }
        },

        /**
         * Initialize particular columns list
         *
         * @param {Object} entity target columns
         * @returns {void}
         */
        _initColumns: function (entity) {
            var self = this;

            _.each(Object.keys(entity.columns()), function (key, index) {
                self._initColumn(entity.columns()[key], entity, index);
            });
        },

        /**
         * Initialize particular column
         *
         * @param {Object} column target column
         * @param {Object} entity column entity
         * @param {Number} index column
         * @returns {void}
         */
        _initColumn: function (column, entity, index) {
            column.index = index;
            column.title = ko.observable(column.title);
            column.entity_index = entity.index;
            column.isVisible = ko.observable(true);
            column.isHit = ko.observable(false);

            if (!column.isDisabled) {
                column.isDisabled = ko.observable(false);
            }

            if (column.entity_index === 0 && column.use_for_period) {
                this._initPeriodColumn(column);
            }

            if (column.entity_index === 0 && column.primary) {
                this.mainEntity.primaryColumn(column);

                if (!this.source.data.chosen_data || !this.source.data.chosen_data.length) {
                    this.chosenOptions.initColumn(column);
                    column.sortStatus(2);
                    this.setPrimaryColumn(column);
                }
            }
        },

        /**
         * Choose and add target column to chosen list via Chosen Component Method
         *
         * @param {Object} prototype target prototype
         * @returns {void}
         */
        chooseColumn: function (prototype) {
            this.chosenOptions.addColumn(prototype);
        },

        /**
         * Setting Primary column
         *
         * @param {Object} column target column
         * @returns {void}
         */
        setPrimaryColumn: function (column) {
            var self = this;

            self.chosenOptions.initColumn(column);

            if (self.currentPrimaryColumn()) {
                self.currentPrimaryColumn().isDisabled(false);
            }

            if (column.position !== undefined) {
                self.chosenOptions.removeColumn(column);
            }

            self.currentPrimaryColumn(column);

            column.isDisabled(true);
        },

        /**
         * Initialize Drag and Drop functionality for target column
         *
         * @param {Object} item prototype column element
         * @param {Object} nodeElement
         * @returns {void}
         */
        initDnD: function (item, nodeElement) {
            var self = this;

            $(nodeElement).draggable({
                helper: 'clone',
                zIndex: 3,
                stack: self.selectors.chosenBlock,
                connectToSortable: self.selectors.chosenList,
                start: function () {
                    self.isDragging(true);
                },
                stop: function () {
                    self.isDragging(false);
                }
            }).data({
                item: item
            });
        },

        /**
         * Remove an entry from the target entity chosen column list by target column index
         *
         * @param {Number} entityIndex target entity index
         * @param {Number} columnIndex
         * @returns {void}
         */
        removeChosenColumn: function (entityIndex, columnIndex) {
            this.elems()[entityIndex].chosenColumnsList.remove(function (item) {
                return item === columnIndex;
            });
        },

        /**
         * Get entity by name
         *
         * @param {String} entityName
         * @returns {String}
         */
        getEntityByName: function (entityName) {
            return ko.utils.arrayFirst(this.elems(), function (entity) {
                return entity.name === entityName;
            });
        },

        /**
         * Get main entity with relations by entity name
         *
         * @param {String} entityName entity name
         * @param {Function} callback target function for preparing data
         * @returns {void}
         *
         */
        getEntity: function (entityName, callback) {
            var self = this;

            $.ajax({
                url: self.source.main_entity_url,
                method: 'POST',
                data: {
                    entityName: entityName
                },
                showLoader: true
            }).done(function (data) {
                callback(data);
            });
        },

        /**
         * Get Column by target index from target entity
         *
         * @param {Number} entityIndex
         * @param {Number} columnIndex
         * @returns {Object} particular column
         */
        getColumnByIndex: function (entityIndex, columnIndex) {
            return this.elems()[entityIndex].columns()[Object.keys(this.elems()[entityIndex].columns())[columnIndex]];
        },

        /**
         * Clearing all chosen options from target entity
         *
         * @param {Number} entityIndex entity
         * @returns {void}
         */
        clearEntity: function (entityIndex) {
            var self = this,
                column,
                entity = this.elems()[entityIndex],
                entityChosenList = _.clone(entity.chosenColumnsList());

            _.each(entityChosenList, function (columnIndex) {
                column = self.getColumnByIndex(entityIndex, columnIndex);

                if (column) {
                    self.chosenOptions.removeColumn(column);
                }
            });
        },

        /**
         * Clearing Columns properties
         *
         * @param {Number} entityIndex
         * @param {String} columnName
         * @returns {void}
         */
        clearColumn: function (entityIndex, columnName) {
            var column = this.elems()[entityIndex].columns()[columnName];

            if (_.isUndefined(column)) {
                return;
            }

            column.isDisabled(false);

            if (column.position !== undefined) {
                column.isDate(false);
                column.sortStatus(0);
                column.isVisible(true);
                delete column.position;
            }

            if (column.customTitle) {
                column.isEdited(false);
                column.customTitle = null;
            }

            if (column.aggregation) {
                this.toolbar.clearAggregation(column);
            }

            if (column.filtration) {
                this.toolbar.clearFiltration(column);
            }
        },

        /**
         * Main entity initialization
         *
         * @param {Object} entity target entity
         * @returns {void}
         */
        _initMainEntity: function (entity) {
            entity.isActive(true);

            this.mainEntity.entity_name = entity.name;
        },

        /**
         * Use period column initialization
         *
         * @param {Object} column target column
         * @returns {void}
         */
        _initPeriodColumn: function (column) {
            this.isUsePeriod.visible(true);
            this.isUsePeriod.loaded = true;
            this.mainEntity.periodColumn(column);
        }
    });
});
