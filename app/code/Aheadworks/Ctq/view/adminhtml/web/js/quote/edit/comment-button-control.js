/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'uiRegistry'
], function($, alert, registry) {

    $.widget('awctq.awCtqCommentButtonControl', {
        options: {
            formSelector: '',
            formAction: '',
            commentWrapperSelector: '.aw-ctq__comments_wrapper',
            commentInputSelector: '[name="comment"]'
        },

        formObject: null,

        /**
         * Initialize widget
         */
        _create: function() {
            this.formObject = $(this.options.formSelector);

            if (!this.formObject.length) {
                return;
            }
            this._bind();
        },

        /**
         * Event binding
         */
        _bind: function() {
            this._on(this.element, {
                'click': this._onButtonClick
            });
        },

        /**
         * Click event handler
         *
         * @param {Object} event
         */
        _onButtonClick: function(event) {
            event.preventDefault();
            this._submitForm();
        },

        /**
         * Submit form
         */
        _submitForm: function () {
            var self = this,
                action = this.options.formAction || this.formObject.attr('action'),
                requestData = this._serializeData(this.formObject),
                uiUploader;

            requestData['form_key'] = window.FORM_KEY;

            $.ajax({
                url: action,
                type: 'POST',
                dataType: 'json',
                data: requestData,

                /**
                 * Before send callback
                 */
                beforeSend: function() {
                    $('body').trigger('processStart');
                },

                /**
                 * Success callback.
                 * @param {Object} response
                 * @returns {Boolean}
                 */
                success: function(response) {
                    if (response.error) {
                        alert({ content: response.message });
                    } else {
                        $(self.options.commentWrapperSelector).html(response.content);
                        $(self.options.commentInputSelector).val('');
                        uiUploader = registry.get('awCtqFileUploader');
                        if (uiUploader) {
                            uiUploader.value([]);
                        }
                    }
                },

                /**
                 * Complete callback
                 */
                complete: function () {
                    $('body').trigger('processStop');
                }
            });
        },

        /**
         * Serializes container form elements data.
         *
         * @param {String} form
         * @return {Object}
         */
        _serializeData: function (form) {
            var data = {};

            $.each(form.serializeArray(), function(i, field){
                data[field['name']] = field['value'];
            });
            return data;
        }
    });

    return $.awctq.awCtqCommentButtonControl;
});
