<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Address;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\Address\CollectionFactory as AddressCollectionFactory;

/**
 * Class Loader
 *
 * @package Aheadworks\Ctq\Model\Quote\Address
 */
class Loader
{
    /**
     * @var AddressCollectionFactory
     */
    private $addressCollectionFactory;

    /**
     * @param AddressCollectionFactory $addressCollectionFactory
     */
    public function __construct(
        AddressCollectionFactory $addressCollectionFactory
    ) {
        $this->addressCollectionFactory = $addressCollectionFactory;
    }

    /**
     * Load quote shipping address
     *
     * @param Quote $quote
     * @param string $addressType
     * @return AddressInterface
     */
    public function loadByType($quote, $addressType)
    {
        $addresses = $this->addressCollectionFactory->create()->setQuoteFilter($quote->getId());
        $shippingAddress = $quote->getShippingAddress();
        foreach ($addresses as $address) {
            if ($address->getAddressType() == $addressType && !$address->isDeleted()) {
                $shippingAddress = $address;
            }
        }

        return $shippingAddress;
    }
}
