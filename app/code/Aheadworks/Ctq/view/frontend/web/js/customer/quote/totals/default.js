/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/totals'
], function (Component, totals) {
    "use strict";

    return Component.extend({

        /**
         * Order totals
         *
         * @return {Object}
         */
        totals: totals.totals(),

        /**
         * Is display shipping totals
         *
         * @return {Boolean}
         */
        isDisplayed: function() {
            return this.isFullMode() && this.getPureValue() != 0;
        },

        /**
         * Get total value
         *
         * @return {Number}
         */
        getPureValue: function() {
            var price = 0;

            if (this.totals) {
                var total = totals.getSegment(this.code);

                if (total) {
                    price = total.value;
                }
            }
            return price;
        },

        /**
         * Get total value
         *
         * @return {String}
         */
        getValue: function() {
            return this.getFormattedPrice(this.getPureValue());
        }
    });
});
