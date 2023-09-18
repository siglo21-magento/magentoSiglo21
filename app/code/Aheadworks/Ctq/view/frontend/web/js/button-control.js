/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function($, confirm) {

    $.widget('awctq.awCtqButtonControl', {
        options: {
            newLocation: '',
            submitForm: {
                formSelector: '',
                actionType: '',
                action: ''
            },
            confirm: {
                enabled: false,
                message: ''
            }
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this._bind();
        },

        /**
         * Event binding
         */
        _bind: function() {
            this._on({
                'click': '_onButtonClick'
            });
        },

        /**
         * Click event handler
         *
         * @param {Object} event
         */
        _onButtonClick: function(event) {
            var self = this;

            event.preventDefault();
            if (this.options.confirm.enabled) {
                if (this._isValidForm()) {
                    confirm({
                        modalClass: 'confirm aw-ctq__confirm',
                        content: this.options.confirm.message,
                        actions: {
                            confirm: function () {
                                self._action();
                            }
                        },
                        buttons: [
                            {
                                text: $.mage.__('No'),
                                class: 'action-secondary action-dismiss',
                                click: function (event) {
                                    this.closeModal(event);
                                }
                            },
                            {
                                text: $.mage.__('Yes'),
                                class: 'action-primary action-accept',
                                click: function (event) {
                                    this.closeModal(event, true);
                                }
                            }
                        ]
                    });
                }
            } else {
                this._action();
            }
        },

        /**
         * Submit form
         */
        _submitForm: function () {
            if ($(this.options.submitForm.formSelector).length) {
                if (!_.isEmpty(this.options.submitForm.action)) {
                    $(this.options.submitForm.formSelector).attr('action', this.options.submitForm.action);
                }

                if (this._isValidForm()) {
                    $(this.options.submitForm.formSelector).submit();
                    $('body').trigger('processStart');
                }
            } else {
                this._redirectToUrl(this.options.submitForm.action);
            }
        },

        /**
         * Check if form valid
         *
         * @return {Boolean}
         */
        _isValidForm: function () {
            if (_.isEmpty(this.options.submitForm.formSelector)) {
                return true;
            }

            var event = $.Event('additional.validation'),
                isValid;

            // Validate UI component from form
            $(this.options.submitForm.formSelector).trigger(event);
            isValid = $(this.options.submitForm.formSelector).valid();

            return event.isDefaultPrevented() == false && isValid;
        },


        /**
         * Button action
         */
        _action: function () {
            if (this.options.submitForm.actionType === 'export') {
                window.location = this.options.submitForm.action;
            } else if (!_.isEmpty(this.options.submitForm.formSelector)) {
                this._submitForm();
            }  else {
                this._redirectToUrl(this.options.newLocation);
            }
        },

        /**
         * Redirect to url
         * @param {String} url
         */
        _redirectToUrl: function (url) {
            window.location = url;
            $('body').trigger('processStart');
        }
    });

    return $.awctq.awCtqButtonControl;
});
