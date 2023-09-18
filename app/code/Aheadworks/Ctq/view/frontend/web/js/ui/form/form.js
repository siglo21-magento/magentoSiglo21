/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/customer-data'
], function ($, Component, customerData) {
    'use strict';

    return Component.extend({
        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super()
                ._addFormKeyIfNotSet();

            return this;
        },

        /**
         * Add form key to window object if form key is not added earlier
         * Used for submit request validation
         *
         * @returns {Form} Chainable
         */
        _addFormKeyIfNotSet: function () {
            if (!window.FORM_KEY) {
                window.FORM_KEY = $.mage.cookies.get('form_key');
            }
            return this;
        },

        /**
         * @inheritDoc
         */
        submit: function (redirect) {
            customerData.invalidate(['quote-list', 'cart']);
            this._super(redirect);
        }
    });
});
