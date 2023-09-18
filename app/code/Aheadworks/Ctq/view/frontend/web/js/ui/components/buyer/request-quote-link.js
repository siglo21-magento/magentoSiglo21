/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'uiComponent'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_Ctq/components/buyer/request-quote-link',
            modules: {
                awCtqRequestQuote: 'awCtqRequestQuoteParent.awCtqRequestQuote'
            }
        },

        /**
         * Open modal action
         */
        openModal: function () {
            this.awCtqRequestQuote().openModal();
        }
    });
});