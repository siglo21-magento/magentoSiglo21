/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */


define([
    'mage/translate',
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mageUtils'
], function ($t, $, alert, utils) {
    'use strict';

    return function (deferred, data) {
        $.ajax({
            url: data.validateEmailUrl,
            type: "POST",
            data: utils.serialize(data.params),
            dataType: 'json',

            /**
             * Success callback.
             *
             * @param {Object} response
             * @returns {Boolean}
             */
            success: function (response) {
                if (response.error) {
                    alert({
                        content: response.message,
                    });
                    deferred.resolve();
                }
                if (response['available_for_' + data.emailType]) {
                    deferred.resolve();
                } else {
                    deferred.reject();
                }
            },

            /**
             * Error callback.
             *
             * @param {Object} response
             * @returns {Boolean}
             */
            error: function (response) {
                alert({
                    title: $t('There has been an error'),
                    content: response.statusText,
                });
                deferred.resolve();
            }
        });
    };
});
