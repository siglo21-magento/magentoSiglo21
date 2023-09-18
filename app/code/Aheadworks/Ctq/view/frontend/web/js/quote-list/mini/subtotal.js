/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (ko, Component, customerData) {
    'use strict';

    return Component.extend({
        displaySubtotal: ko.observable(true),

        /**
         * @override
         */
        initialize: function () {
            this._super();
            this.cart = customerData.get('quote-list');
        }
    });
});
