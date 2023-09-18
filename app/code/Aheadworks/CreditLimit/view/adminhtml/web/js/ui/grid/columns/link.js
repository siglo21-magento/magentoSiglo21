/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_CreditLimit/ui/grid/columns/cells/link'
        },

        /**
         * Retrieve label for column
         *
         * @returns {String}
         */
        getLabel: function(row) {
            return row[this.index + '_label'];
        },

        /**
         * Retrieve url for column
         *
         * @returns {String}
         */
        getUrl: function(row) {
            return row[this.index + '_url'];
        }
    });
});
