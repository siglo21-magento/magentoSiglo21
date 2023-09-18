/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery'
], function($) {

    $.widget('awctq.awCtqAddToQuoteListButton', {
        options: {
            sku: 'empty',
            searchPattern: '[data-product-sku={sku}]',
            isProductPage: false,
            miniQuoteListSelector: '[data-block=\'mini-quote-list\']'
        },

        /**
         * @inheritDoc
         */
        _create: function () {
            this._bind();
            if (!this.options.isProductPage) {
                this._moveButton();
            }
            this._enableButton();
        },

        /**
         * @private
         */
        _bind: function () {
            this._on({
                'click': '_onButtonClick'
            });
        },

        /**
         * Click event handler
         *
         * @param {Object} event
         */
        _onButtonClick: function (event) {
            var form = this._getForm(),
                isValid;

            if (form.length) {
                isValid = this.options.isProductPage ? form.validation('isValid') : true;
                event.preventDefault();
                if (isValid) {
                    this._ajaxSubmit(form);
                }
            }
        },

        /**
         * Retrieve form
         *
         * @return {HTMLFormElement | Element}
         * @private
         */
        _getForm: function () {
            var form = this.element.closest('form');

            return form.length ? form : $(this.options.searchPattern.replace('{sku}', this.options.sku));
        },

        /**
         * Submit data
         *
         * @param {jQuery} form
         * @private
         */
        _ajaxSubmit: function (form) {
            var formData = new FormData(form[0]),
                self = this;

            $.ajax({
                url: this._getUrl(form),
                data: formData,
                type: 'post',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,

                /**
                 * @inheritDoc
                 */
                beforeSend: function () {
                    $('body').trigger('processStart');
                    $(self.options.miniQuoteListSelector).trigger('contentLoading');
                    self._disableButton();
                },

                /**
                 * @inheritDoc
                 */
                complete: function (response) {
                    if (response.state() === 'rejected') {
                        location.reload();
                    }
                    if (response.responseJSON.backUrl) {
                        location.replace(response.responseJSON.backUrl);
                    }
                    $('body').trigger('processStop');
                    self._enableButton();
                }
            });
        },

        /**
         * Get add to quote list url
         *
         * @param {Object} form
         * @return {String}
         */
        _getUrl: function (form) {
            var formAction = form.attr('action');

            formAction = formAction.replace(
                'checkout/cart/add',
                'aw_ctq/quoteList/add'
            );

            return formAction;
        },

        /**
         * Move button
         * @private
         */
        _moveButton: function () {
            var buttonContainer = $(this.element).parent();

            buttonContainer.insertBefore(buttonContainer.parent());
        },

        /**
         * Enable button
         * @private
         */
        _enableButton: function () {
            this.element.removeAttr('disabled');
        },

        /**
         * Disable button
         * @private
         */
        _disableButton: function () {
            this.element.attr('disabled', 'disabled');
        }
    });

    return $.awctq.awCtqAddToQuoteListButton;
});
