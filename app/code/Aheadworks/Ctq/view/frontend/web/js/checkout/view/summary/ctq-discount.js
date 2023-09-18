/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/totals'
], function (Component, totals) {
    "use strict";

    return Component.extend({
        defaults: {
            template: 'Aheadworks_Ctq/checkout/view/summary/ctq-discount'
        },

        /**
         * Order totals
         *
         * @return {Object}
         */
        totals: totals.totals(),

        /**
         * Is display raf totals
         *
         * @return {Boolean}
         */
        isDisplayed: function() {
            return this.isFullMode() && this.getPureValue() != 0;
        },

        /**
         * Get total pure value
         *
         * @return {Number}
         */
        getPureValue: function() {
            var price = 0,
                negotiatedDiscount;

            if (this.totals) {
                negotiatedDiscount = totals.getSegment('aw_ctq');

                if (negotiatedDiscount) {
                    price = negotiatedDiscount.value;
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
