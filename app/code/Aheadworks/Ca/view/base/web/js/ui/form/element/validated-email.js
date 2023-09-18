/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'Aheadworks_Ca/js/action/check-email-availability',
    'Magento_Ui/js/lib/validation/validator',
], function($, _, Element, checkEmailAvailability, validator) {
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
        'aw-ca-validate-email',
        validateCallback,
        $.mage.__('The email is already registered.')
    );

    return Element.extend({
        defaults: {
            emailType: '',
            initialValue: '',
            validateEmailUrl: '',
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
            var isEmailCheckComplete = $.Deferred(),
                data = {
                params: {
                    email: this.value(),
                    website_id: this.source.data.website_id ? this.source.data.website_id : null,
                },
                emailType: this.emailType,
                validateEmailUrl: this.validateEmailUrl
            };

            $("body").trigger('processStart');
            checkEmailAvailability(isEmailCheckComplete, data);

            $.when(isEmailCheckComplete).done(function () {
                this.isEmailValid(true);
            }.bind(this)).fail(function () {
                this.isEmailValid(false);
            }.bind(this)).always(function () {
                $("body").trigger('processStop');
                this.validate();
            }.bind(this));
        }
    });
});
