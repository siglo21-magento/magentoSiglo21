/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
], function ($, _, Form, confirmation, $t) {
    'use strict';

    return Form.extend({

        /**
         * Validate and save form.
         *
         * @param {String} redirect
         * @param {Object} data
         */
        save: function (redirect, data) {
            var isConfirm = this.source['isConfirm'] || false,
                self = this;

            this.validate();

            if (!this.additionalInvalid && !this.source.get('params.invalid')) {
                if (isConfirm) {
                    this.showConfirmWindow(function () {
                        self.setAdditionalData(data).submit(redirect);
                    })
                }
                else {
                    this.setAdditionalData(data).submit(redirect);
                }
            } else {
                this.focusInvalid();
            }
        },

        /**
         * Init and show confirm window
         *
         * @param confirmCallback
         */
        showConfirmWindow: function (confirmCallback) {
            confirmation({
                clickableOverlay: false,
                title: $.mage.__('Do you want to assign?'),
                content: 'Do you want to assign this company to the existing account?',
                buttons: [{
                    text: $t('Cancel'),
                    class: 'action-secondary action-dismiss',
                    click: function (event) {
                        this.closeModal(event);
                    }
                }, {
                    text: $t('Save'),
                    class: 'action-primary action-accept',
                    click: function (event) {
                        this.closeModal(event, true);
                    }
                }],
                actions: {
                    confirm: function () {
                        confirmCallback();
                    },
                }
            });
        }
    });
});
