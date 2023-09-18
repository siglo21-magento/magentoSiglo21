/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define
([
    'jquery',
    'jquery/ui',
    'Aheadworks_Ctq/js/quote/edit/form'
], function ($) {
    'use strict';

    $.widget('mage.negotiatedDiscountSelector', {

        options: {
            total: 0,
            errorMessage: '',
        },

        /**
         * Create widget
         */
        _create: function () {
            var self = this,
                rows = this.element.find('tr');

            rows.each(function () {
                var row = $(this),
                    radioElement = row.find('input[type="radio"]'),
                    inputElement = row.find('input[type="number"]');

                inputElement.prop('disabled', !radioElement.prop('checked') || radioElement.prop('disabled'));
                inputElement.on('keyup', self.onPriceChange.bind(self, inputElement));
                inputElement.on('change', self.applyPrice.bind(self, inputElement));
                radioElement.on('change', self.onRadioButtonChange.bind(self, radioElement, inputElement));
                self._validateElement(inputElement);
            });
        },

        /**
         * Callback handler on price change
         *
         * @param {Object} element
         * @public
         */
        onPriceChange: function (element) {
            if (this._validateField(element)) {
                element.addClass('hasError');
                this.showErrorMessage(this.options.errorMessage);
            } else {
                element.removeClass('hasError');
                this.hideErrorMessage();
            }
        },

        /**
         * Callback handler on radio button change
         *
         * @param {Object} radioElement
         * @param {Object} relatedInputElement
         * @public
         */
        onRadioButtonChange: function (radioElement, relatedInputElement) {
            relatedInputElement.prop('disabled', false);
            this.element.find('input[type="number"]').not(relatedInputElement).prop('disabled', true).removeClass('hasError');
            this.hideErrorMessage();
            this._validateElement(relatedInputElement);
        },

        /**
         * Callback handler on price change complete to apply price
         *
         * @param {Object} element
         * @public
         */
        applyPrice: function (element) {
            if (!this._validateField(element)) {
                quote.setNegotiationDiscount(
                    element.attr('data-discount-type'),
                    element.val()
                )
            }
        },

        /**
         * Validate field
         *
         * @param {Object} inputElement
         * @returns {Boolean}
         * @private
         */
        _validateField: function (inputElement) {
            var value = inputElement.val(),
                result;

            switch (inputElement.attr('data-discount-type')) {
                case 'percent':
                    result = this._validateValue(value, 100);
                    if (result) {
                        this.options.errorMessage = this.options.errorText.percent;
                    }
                    return result;

                case 'amount':
                case 'proposed_price':
                    result = this._validateValue(value, this.options.total);
                    if (result) {
                        this.options.errorMessage = this.options.errorText.amount;
                    }
                    return result;
                default:
                    return false;
            }
        },

        /**
         * Validate element
         *
         * @param {Object} element
         */
        _validateElement: function (element) {
            if (element.val()) {
                if (this._validateField(element)) {
                    this.showErrorMessage(this.options.errorMessage);
                } else {
                    this.hideErrorMessage();
                }
            }
        },

        /**
         * Validate value
         *
         * @param {String} value
         * @param {Number} total
         * @returns {Boolean}
         * @private
         */
        _validateValue: function (value, total) {
            var number = parseFloat(value);

            return number < 0 || number > total;
        },

        /**
         * Hide error message
         */
        hideErrorMessage: function () {
            $(this.element).find('.error-message').hide();
        },

        /**
         * Show error message
         *
         * @param {string} text
         */
        showErrorMessage: function (text) {
            $(this.element).find('.error-text').html(text);
            $(this.element).find('.error-message').show();
        },
    });

    return $.mage.negotiatedDiscountSelector;
});
