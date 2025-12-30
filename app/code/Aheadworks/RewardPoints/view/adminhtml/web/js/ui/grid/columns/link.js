define([
    'Magento_Ui/js/grid/columns/column'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_RewardPoints/ui/grid/cells/link'
        },

        /**
         * Get field handler
         *
         * @param record
         * @returns {boolean}
         */
        getFieldHandler: function (record) {
            return false;
        },

        /**
         * Retrieve link rows
         *
         * @returns {Array}
         */
        getLinkRows: function (record) {
            if (record[this.index] && !Array.isArray(record[this.index])) {
                return [record[this.index]];
            }
            return record[this.index];
        },
    });
});
