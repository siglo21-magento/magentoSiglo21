/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'Magento_Ui/js/lib/validation/validator',
    'Magento_Ui/js/modal/alert',
    'mageUtils',
    'mage/translate',
], function($, _, Element, validator, alert, utils, $t) {
    "use strict";

    /**
     * Validate if element has an attribute set up
     *
     * @param {*} value
     * @param {*} params
     * @param {String} elementId
     * @returns {String|$}
     */
    var validateCallback = function (value, params, elementId) {
        return $(elementId).attr('is-email-valid');
    };

    validator.addRule(
        'aw-ca-validate-email-check-convert',
        validateCallback,
        $.mage.__('The email is already registered.')
    );

    return Element.extend({
        defaults: {
            map: {
                firstname: {customer: "firstname", data: "firstname"},
                lastname: {customer: "lastname", data: "lastname"}
            },
            checkUrl: '',
            initialValue: '',
            isEmailValid: true,
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            this.validationParams = '#' + this.uid;

            return this;
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe(['isEmailValid']);

            return this;
        },

        /**
         *  Callback when value is changed by user
         */
        emailHasChanged: function() {
            if (this.valueChangedByUser) {
                if (this.value() !== this.initialValue) {
                    this.validateEmail();
                } else {
                    this.isEmailValid(true);
                    this.validate();
                }
            }
        },

        /**
         * Validate email
         */
        validateEmail: function() {
            var data = {
                    email: this.value(),
                    website_id: this.source.data.website_id ? this.source.data.website_id : null,
                },
                self = this;

            this.request(data, function (isAvailable, isAvailableForConvert, customer) {
                if (!isAvailable && isAvailableForConvert && self.isEnabled()) {
                    self.isEmailValid(true);
                    self.source.set('isConfirm', true);
                    self.source.set('data.' + self.map.firstname.data, customer[self.map.firstname.customer]);
                    self.source.set('data.' + self.map.lastname.data, customer[self.map.lastname.customer]);
                } else if (!isAvailable) {
                    self.isEmailValid(false);
                    self.source.set('isConfirm', false);
                } else {
                    self.isEmailValid(true);
                    self.source.set('isConfirm', false);
                }
                self.validate();
            });
        },

        /**
         *
         * @returns {Boolean}
         */
        isEnabled: function() {
            return Boolean(this.source.get('data.isEnabledConverter'));
        },

        /**
         * Send ajax request
         *
         * @param data
         * @param successCallback
         */
        request: function (data, successCallback) {
            $("body").trigger('processStart');
            $.ajax({
                url: this.checkUrl,
                type: "POST",
                data: utils.serialize(data),
                dataType: 'json',

                /**
                 * Success callback.
                 *
                 * @param {Object} response
                 * @returns {Boolean}
                 */
                success: function (response) {
                    $("body").trigger('processStop');
                    if (response.error) {
                        alert({
                            content: response.error,
                        });
                    } else {
                        successCallback(
                            response['isAvailable'],
                            response['isAvailableForConvert'],
                            response['customer'],
                        );
                    }
                },

                /**
                 * Error callback.
                 *
                 * @param {Object} response
                 * @returns {Boolean}
                 */
                error: function (response) {
                    $("body").trigger('processStop');
                    alert({
                        title: $t('There has been an error'),
                        content: response.statusText,
                    });
                }
            });
        }
    });
});
