/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'underscore',
    'uiElement',
    'Magento_Catalog/js/price-utils'
], function (_, Element, priceUtils) {
    'use strict';

    return Element.extend({
        defaults: {
            imports: {
                basePriceFormat: '${ $.provider }:data.aw_credit_limit.basePriceFormat',
                priceFormat: '${ $.provider }:data.aw_credit_limit.priceFormat'
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .track(['basePriceFormat', 'priceFormat']);

            return this;
        },

        /**
         * Prepare value for total
         *
         * @param {Object} data - Data to be preprocessed
         * @returns {String}
         */
        getValue: function (data) {
            return this.formatValue(data[this.index], this.dataType, false);
        },

        /**
         * Prepare converted value for total
         *
         * @param {Object} data - Data to be preprocessed
         * @returns {String}
         */
        getConvertedValue: function (data) {
            var value = data[this.index + '_converted'];

            return value ? '(' + this.formatValue(value, this.dataType, true) + ')' : '';
        },

        /**
         * Format value by type
         *
         * @param {String} value
         * @param {String} type
         * @param {Boolean} isConverted
         * @returns {String}
         */
        formatValue: function (value, type, isConverted) {
            var formattedValue,
                priceFormat = isConverted ? this.priceFormat : this.basePriceFormat;

            switch (type) {
                case 'price':
                    formattedValue = priceUtils.formatPrice(value, priceFormat);
                    break;
                default:
                    formattedValue = String(Number(value * 1).toFixed(0));
            }

            return formattedValue;
        }
    });
});
