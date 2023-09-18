/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'ko',
    'underscore',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'sidebar',
    'mage/translate',
    'mage/dropdown'
], function ($, ko, _, Component, customerData) {
    'use strict';

    var sidebarInitialized = false,
        addToQuoteListCalls = 0;

    /**
     * Prepare url
     *
     * @param {string} url
     * @return {string}
     */
    function prepareUrl(url) {
        var urlObj = new URL(url);

        if (!urlObj.searchParams.has('aw_ctq_quote_list')) {
            urlObj.searchParams.append('aw_ctq_quote_list', '1');
            return urlObj.toString();
        } else {
            return url;
        }
    }

    return Component.extend({
        quoteListUrl: window.checkout.quoteListUrl,
        maxItemsToDisplay: window.checkout.maxItemsToDisplay,
        isLoading: ko.observable(false),
        quoteList: {},
        contentSelector: '[data-block="mini-quote-list"]',
        defaults: {
            sidebarConfig: {
                'targetElement': 'div.block.block-minicart',
                'url': {
                    'update': prepareUrl(window.checkout.updateItemQtyUrl),
                    'remove': prepareUrl(window.checkout.removeItemUrl)
                },
                'button': {
                    'remove': '#mini-quote-list a.action.delete',
                    'close': '#btn-mini-quote-list-close'
                },
                'minicart': {
                    'list': '#mini-quote-list',
                    'maxItemsVisible': window.checkout.minicartMaxItemsVisible
                },
                'item': {
                    'qty': ':input.cart-item-qty',
                    'button': ':button.update-cart-item'
                },
                'confirmMessage': $.mage.__('Are you sure you would like to remove this item?')
            }
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            var self = this,
                quoteListData = customerData.get('quote-list'),
                miniQuoteList = $(this.contentSelector);

            this.updateQuoteList(quoteListData());
            quoteListData.subscribe(function (updatedQuoteList) {
                self.updateQuoteList(updatedQuoteList);
                self.initSidebar();
                addToQuoteListCalls--;
                self.isLoading(addToQuoteListCalls > 0);
                sidebarInitialized = false;
            }, this);
            $(this.contentSelector).on('contentLoading', function () {
                addToQuoteListCalls++;
                self.isLoading(true);
            });

            if (
                quoteListData().website_id !== window.checkout.websiteId &&
                quoteListData().website_id !== undefined
            ) {
                customerData.reload(['mini-quote-list'], false);
            }
            miniQuoteList.on('dropdowndialogopen', function () {
                self.initSidebar();
            });

            return this._super();
        },

        /**
         * @return {Boolean}
         */
        initSidebar: function () {
            var miniQuoteList = $(this.contentSelector);

            if (miniQuoteList.data('mageSidebar')) {
                miniQuoteList.sidebar('update');
            }

            if (!$('[data-role=product-item]').length) {
                return false;
            }
            miniQuoteList.trigger('contentUpdated');

            if (sidebarInitialized) {
                return false;
            }
            sidebarInitialized = true;
            miniQuoteList.sidebar(this.sidebarConfig);
        },

        /**
         * @param {String} productType
         * @return {*|String}
         */
        getItemRenderer: function (productType) {
            return this.itemRenderer[productType] || 'defaultRenderer';
        },

        /**
         * @inheritDoc
         */
        closeMiniQuoteList: function () {
            $(this.contentSelector).find('[data-role="dropdownDialog"]').dropdownDialog('close');
        },

        /**
         * @inheritDoc
         */
        updateQuoteList: function (updatedQuoteList) {
            _.each(updatedQuoteList, function (value, key) {
                if (!_.has(this.quoteList, key)) {
                    this.quoteList[key] = ko.observable();
                }
                if (key === 'items' && !_.isEmpty(value)) {
                    value = this.prepareConfigureUrl(value);
                }
                this.quoteList[key](value);
            }, this);
        },

        /**
         * Prepare configure url
         *
         * @param items
         * @return {Array}
         */
        prepareConfigureUrl: function (items) {
            return _.each(items, function (item) {
                item.configure_url = prepareUrl(item.configure_url);
            });
        },

        /**
         * @inheritDoc
         */
        getQuoteListParam: function (key) {
            if (!_.isUndefined(key)) {
                if (!_.has(this.quoteList, key)) {
                    this.quoteList[key] = ko.observable();
                }
            }

            return this.quoteList[key]();
        },

        /**
         * @returns []
         */
        getQuoteListItems: function () {
            var items = this.getQuoteListParam('items') || [];

            return items.slice(parseInt(-this.maxItemsToDisplay, 10));
        },
    });
});
