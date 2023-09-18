define(
    [
        'jquery',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/url',
        'Magento_Checkout/js/model/error-processor'
    ],
    function ($, customer, quote, urlBuilder, urlFormatter, errorProcessor) {
        'use strict';

        return {

            /**
             * Make an ajax PUT request to store the order comment in the quote.
             *
             * @returns {Boolean}
             */
            validate: function () {
                var isCustomer = customer.isLoggedIn();
                var form = $('.order-comment-form');

                var quoteId = quote.getQuoteId();
                var url = BASE_URL +'aventi_ordercomment/index/index';

                /*if (isCustomer) {

                } else {
                    url = urlBuilder.createUrl('/aventi-ordercomment/ordercomment', {});
                }*/

                var payload = {
                    cartId: quoteId,
                    orderComment: {
                        comment: form.find('.input-text.order-comment').val()
                    }
                };

                if (!payload.orderComment.comment) {
                    return true;
                }

                var result = true;
                console.log(form.find('.input-text.order-comment').val());

                $.ajax({
                    type: "POST",
                    url: url,
                    data: { comment: form.find('.input-text.order-comment').val() },
                    contentType: 'json',
                    async: false
                }).done(
                    function (response) {
                        result = true;
                    }
                ).fail(
                    function (response) {
                        result = false;
                        errorProcessor.process(response);
                    }
                );
                return result;
            }
        };
    }
);