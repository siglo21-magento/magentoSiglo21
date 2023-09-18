/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

require([
    'jquery',
    'Magento_Customer/js/customer-data',
    'underscore',
    'domReady!'
], function ($, customerData, _) {
    'use strict';

    var qtySelector = '#product_addtocart_form [name="qty"]',
        productIdSelector = '#product_addtocart_form [name="product"]',
        itemIdSelector = '#product_addtocart_form [name="item"]',
        quoteListData = customerData.get('quote-list'),

        /**
         * Set product qty
         *
         * @param {Object} data
         */
        setProductQty = function (data) {
            var itemId = $(itemIdSelector).val(),
                productId = $(productIdSelector).val(),
                productQtyInput = $(qtySelector),
                productQty;

            if (!data || _.isEmpty(data.items)) {
                return;
            }
            _.each(data.items, function (item) {
                if (item.item_id === itemId
                    && item.product_id === productId
                    || item.item_id === productId
                ) {
                    productQty = item.qty;
                }
            });

            if (productQty) {
                productQtyInput.val(productQty);
            }

        };

    quoteListData.subscribe(function (data) {
        setProductQty(data);
    });

    setProductQty(quoteListData());
});
