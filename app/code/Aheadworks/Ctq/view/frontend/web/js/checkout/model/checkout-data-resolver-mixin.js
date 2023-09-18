/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'mage/utils/wrapper',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/create-shipping-address',
], function(wrapper, addressList, quote, checkoutData, createShippingAddress) {
    'use strict';

    return function (dataResolver) {

        dataResolver.resolveShippingAddress = wrapper.wrap(dataResolver.resolveShippingAddress, function(original) {
            if (window.checkoutConfig.isShippingAddressOverridden) {
                var newShippingAddress;

                checkoutData.setShippingAddressFromData(null);
                if (addressList().length !== 0) {
                    newShippingAddress = createShippingAddress(window.checkoutConfig.shippingAddressFromData);
                    checkoutData.setNewCustomerShippingAddress(window.checkoutConfig.shippingAddressFromData);
                    checkoutData.setSelectedShippingAddress(newShippingAddress.getKey())
                }
            }
            return original();
        });

        return dataResolver;
    };
});
