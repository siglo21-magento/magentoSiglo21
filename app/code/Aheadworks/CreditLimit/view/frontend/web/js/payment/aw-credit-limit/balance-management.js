/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Catalog/js/price-utils',
    'Aheadworks_CreditLimit/js/payment/aw-credit-limit/placing-order-availability-checker',
], function (priceUtils, canPlaceOrder) {
    "use strict";

    /**
     * Checkout configuration data
     */
    var paymentData = window.checkoutConfig.payment.aw_credit_limit,
        priceFormat = window.checkoutConfig.priceFormat;

    return {

        /**
         * Get customer available credit balance
         *
         * @return {Number}
         */
        getAvailableBalancePure: function () {
            return paymentData.credit_available
        },

        /**
         * Check if customer can place order
         *
         * @return {Number}
         */
        canPayForOrder: function () {
            return canPlaceOrder(paymentData);
        },

        /**
         * Check if customer can place multishipping orders
         *
         * @return {Number}
         */
        canPayForMultishippingOrders: function (checkoutTotal) {
            return canPlaceOrder(paymentData, checkoutTotal);
        },

        /**
         * Get customer available credit balance formatted
         *
         * @return {String}
         */
        getAvailableBalanceFormatted: function () {
            return this.formatPrice(this.getAvailableBalancePure())
        },

        /**
         * Format price
         *
         * @return {String}
         */
        formatPrice: function(price) {
            return priceUtils.formatPrice(price, priceFormat);
        }
    }
});
