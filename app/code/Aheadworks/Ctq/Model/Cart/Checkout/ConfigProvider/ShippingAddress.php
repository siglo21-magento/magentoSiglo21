<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Cart\Checkout\ConfigProvider;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ctq\Model\Quote\Address\Comparator as AddressComparator;
use Aheadworks\Ctq\Model\Quote\Address\Converter as AddressConverter;
use Aheadworks\Ctq\Model\Quote\Address\Loader as AddressLoader;
use Magento\Quote\Model\Quote\AddressFactory;
use Magento\Quote\Model\Quote\Address;

/**
 * Class ShippingAddress
 *
 * @package Aheadworks\Ctq\Model\Cart\Checkout\ConfigProvider
 */
class ShippingAddress implements ConfigProviderInterface
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var AddressComparator
     */
    private $addressComparator;

    /**
     * @var AddressConverter
     */
    private $addressConverter;

    /**
     * @var AddressLoader
     */
    private $addressLoader;

    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * @param CheckoutSession $checkoutSession
     * @param AddressComparator $addressComparator
     * @param AddressConverter $addressConverter
     * @param AddressLoader $addressLoader
     * @param AddressFactory $addressFactory
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        AddressComparator $addressComparator,
        AddressConverter $addressConverter,
        AddressLoader $addressLoader,
        AddressFactory $addressFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->addressComparator = $addressComparator;
        $this->addressConverter = $addressConverter;
        $this->addressLoader = $addressLoader;
        $this->addressFactory = $addressFactory;
    }

    /**
     * Return configuration array
     *
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getConfig()
    {
        $output = [];
        $quote = $this->checkoutSession->getQuote();
        if ($quote->getExtensionAttributes() && $quote->getExtensionAttributes()->getAwCtqQuote()) {
            $output['isShippingAddressOverridden'] = false;

            $quoteOriginalAddress = $this->addressLoader->loadByType($quote, Address::TYPE_SHIPPING);
            $formattedAddress = $this->addressConverter->convertForCheckoutForm($quoteOriginalAddress);
            if ($quote->getCustomer() && $quote->getCustomer()->getDefaultShipping()) {
                $currentAddress = $quote->getShippingAddress();
                $isOverridden = !$this->addressComparator->isEqual($quoteOriginalAddress, $currentAddress, false);
            } else {
                $isOverridden = !empty($formattedAddress);
            }

            if ($isOverridden) {
                $output['isShippingAddressOverridden'] = true;
                $output['shippingAddressFromData'] = $formattedAddress;
            }
        }

        return $output;
    }
}
