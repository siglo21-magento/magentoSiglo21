/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'underscore',
    'uiCollection'
], function (_, Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Aheadworks_CreditLimit/ui/customer/top-totals',
            imports: {
                topTotals: '${ $.provider }:data.aw_credit_limit.totals'
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .track({topTotals: []});

            return this;
        },

        /**
         * Check if at least one total is visible
         *
         * @return {Boolean}
         */
        isTopTotalsVisible: function () {
            return this.elems().length > 0;
        }
    });
});
