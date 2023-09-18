/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Aheadworks_CreditLimit/js/payment/aw-credit-limit/balance-management',
    'mage/translate',
    'Magento_Ui/js/modal/alert'
], function ($, Component, BalanceManagement, $t, alert) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_CreditLimit/payment/form',
            purchaseOrderNumber: ''
        },

        /** @inheritdoc */
        initObservable: function () {
            this._super()
                .observe('purchaseOrderNumber');

            return this;
        },

        /**
         * @return {Object}
         */
        getData: function () {
            return {
                method: this.item.method,
                'po_number': this.purchaseOrderNumber(),
                'additional_data': null
            };
        },

        /**
         * Get available customer credit balance
         *
         * @return {String}
         */
        getAvailableCreditBalance: function () {
           return $t('Disponible: ') + BalanceManagement.getAvailableBalanceFormatted();
        },

        /**
         * Check if balance is enough to pay for order
         *
         * @return {Boolean}
         */
        isBalanceEnoughToPay: function () {
            if (this.isMultiShipping()) {
                return BalanceManagement.canPayForMultishippingOrders(this.checkoutTotal);
            }

            // adrian.olave@gmail.com
            // balance is available if return true, not check form checkout
            return true;

            //return BalanceManagement.canPayForOrder();
        },

        /**
         * Check is action toolbar is visible
         *
         * @return {Boolean}
         */
        isActionToolbarVisible: function () {
            return this.isBalanceEnoughToPay();
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
         * Is multi shipping
         *
         * @returns {Boolean}
         */
        isMultiShipping: function () {
            return Boolean(JSON.parse(window.checkoutConfig.quoteData.is_multi_shipping));
        },


        /**
         * On click place order button
         */
        placeOrder: function () {
            if (this.isBalanceEnoughToPay()) {
                this._super();
            } else {
                this.showNotEnoughBalanceMessage();
            }
        },

        /**
         * On click place order button while multi shipping
         */
        placeOrderMultiShipping: function () {
            if (this.isBalanceEnoughToPay()) {
                $('#multishipping-billing-form').submit();
            } else {
                this.showNotEnoughBalanceMessage();
            }
        },

        /**
         * Show message when balance is not enough to pay
         */
        showNotEnoughBalanceMessage: function () {
            alert({
                title: $t('The request cannot be processed'),
                content: this.getNotEnoughBalanceMessage(),
            });
        },

        /**
         * Validate form
         *
         * @return {jQuery}
         */
        validate: function () {
            var form = 'form[data-role=aw-credit-limit-order-form]';
            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
