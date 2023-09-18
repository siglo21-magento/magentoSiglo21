/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

var config = {
    map: {
        '*': {
            awCtqButtonControl: 'Aheadworks_Ctq/js/button-control',
            awCtqAddToQuoteListButton: 'Aheadworks_Ctq/js/product/quote-list/add',
            awCtqSorting: 'Aheadworks_Ctq/js/customer/quote/items/sorting'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Aheadworks_Ctq/js/checkout/model/checkout-data-resolver-mixin': true
            }
        }
    },
};
