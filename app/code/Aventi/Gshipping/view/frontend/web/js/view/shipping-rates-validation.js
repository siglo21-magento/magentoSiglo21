/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    'Aventi_Gshipping/js/model/shipping-rates-validator',
    'Aventi_Gshipping/js/model/shipping-rates-validation-rules'
], function (
    Component,
    defaultShippingRatesValidator,
    defaultShippingRatesValidationRules,
    gshippingShippingRatesValidator,
    gshippingShippingRatesValidationRules
) {
    'use strict';

    defaultShippingRatesValidator.registerValidator(' gshipping',  gshippingShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules(' gshipping', gshippingShippingRatesValidationRules);

    return Component;
});
