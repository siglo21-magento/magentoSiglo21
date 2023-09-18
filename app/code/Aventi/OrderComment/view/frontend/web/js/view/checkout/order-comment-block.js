define(
    [
        'jquery',
        'uiComponent'
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Aventi_OrderComment/checkout/order-comment-block'
            }
        });
    }
);
