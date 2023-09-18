/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'jquery/ui'
], function($) {

    $.widget('awctq.awCtqSorting', {
        options: {
            itemListSelector: '.aw-ctq__products-tab .table-wrapper tbody',
            sortableConfig: {
                items: "> tr",
                cursor: 'move',
                axis: 'y',
                handle: '.draggable-handle'
            }
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this._initDnd();
        },

        /**
         * Init drag and drop
         *
         * @private
         */
        _initDnd: function () {
            $(this.options.itemListSelector).sortable(this.options.sortableConfig);
            $(this.options.itemListSelector).sortable('option', 'helper', this.fixWidth);
        },

        /**
         * Fix row width while dragging
         *
         * @param {Event} e
         * @param {jQuery} dragElem
         * @return {jQuery}
         */
        fixWidth: function (e, dragElem) {
            dragElem.children().each(function() {
                $(this).width($(this).width());
            });

            return dragElem;
        }
    });

    return $.awctq.awCtqSorting;
});
