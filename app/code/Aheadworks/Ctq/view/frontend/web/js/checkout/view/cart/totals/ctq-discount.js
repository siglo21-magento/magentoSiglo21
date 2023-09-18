/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Aheadworks_Ctq/js/checkout/view/summary/ctq-discount'
], function (Component) {
    "use strict";

    return Component.extend({
        defaults: {
            template: 'Aheadworks_Ctq/checkout/view/cart/totals/ctq-discount'
        },

        /** @inheritdoc */
        isDisplayed: function () {
            return this.getPureValue() != 0;
        }
    });
});
