/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'underscore',
    'Magento_Sales/order/create/form'
], function ($, Class, alert, $t, _) {
    'use strict';

    return Class.extend({
        defaults: {
            $selector: null,
            selector: 'edit_form',
            container: 'payment_form_aw_credit_limit',
            code: 'aw_credit_limit',
            active: false,
            imports: {
                onActiveChange: 'active'
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this.$selector = $('#' + this.selector);
            this._super().observe(['active']);

            // re-init payment method events
            this.$selector.off('changePaymentMethod.' + this.code)
                .on('changePaymentMethod.' + this.code, this.changePaymentMethod.bind(this));

            return this;
        },

        /**
         * Enable/disable current payment method
         *
         * @param {Object} event
         * @param {String} method
         * @returns {exports.changePaymentMethod}
         */
        changePaymentMethod: function (event, method) {
            this.active(method === this.code);

            return this;
        },

        /**
         * Triggered when payment changed
         *
         * @param {Boolean} isActive
         */
        onActiveChange: function (isActive) {
            if (!isActive) {
                return;
            }

            this.disableEventListeners();
            window.order.addExcludedPaymentMethod(this.code);
            this.enableEventListeners();
        },

        /**
         * Enable form event listeners
         */
        enableEventListeners: function () {
            this.$selector.on('submitOrder.' + this.code, this.submitOrder.bind(this));
        },

        /**
         * Disable form event listeners
         */
        disableEventListeners: function () {
            this.$selector.off('submitOrder');
            this.$selector.off('submit');
        },

        /**
         * Check if place order allowed
         *
         * @return {String}
         */
        getNotEnoughBalanceMessage: function () {
            return $t('Insufficient credit funds');
        },

        /**
         * Show alert message
         *
         * @param {String} message
         */
        showError: function (message) {
            alert({
                content: message
            });
        },

        /**
         * Trigger order submit
         */
        submitOrder: function () {
            if (_.has(window, 'awClIsBalanceEnoughToPay')) {
                if (!window.awClIsBalanceEnoughToPay) {
                    $('body').trigger('processStop');
                    this.showError(this.getNotEnoughBalanceMessage());
                    return false;
                }
            }

            $('#' + this.container).find('[type="submit"]').trigger('click');
        }
    });
});
