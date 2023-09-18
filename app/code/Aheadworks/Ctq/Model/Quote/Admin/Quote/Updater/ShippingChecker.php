<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater;

use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Aheadworks\Ctq\Model\Quote\Address\Comparator as AddressComparator;
use Aheadworks\Ctq\Model\Quote\Address\Converter as AddressConverter;
use Magento\Quote\Model\Quote\AddressFactory;
use Magento\Quote\Model\Quote\Address;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ShippingChecker
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater
 */
class ShippingChecker
{
    /**
     * @var AddressComparator
     */
    private $addressComparator;

    /**
     * @var AddressConverter
     */
    private $addressConverter;

    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * @param AddressComparator $addressComparator
     * @param AddressConverter $addressConverter
     * @param AddressFactory $addressFactory
     */
    public function __construct(
        AddressComparator $addressComparator,
        AddressConverter $addressConverter,
        AddressFactory $addressFactory
    ) {
        $this->addressComparator = $addressComparator;
        $this->addressConverter = $addressConverter;
        $this->addressFactory = $addressFactory;
    }

    /**
     * Process shipping data
     *
     * @param array $shippingData
     * @param $quote
     * @throws LocalizedException
     */
    public function processData(&$shippingData, $quote)
    {
        if (isset($shippingData['shipping_address'])) {
            $newAddress = $this->prepareAddress($shippingData['shipping_address'], Address::TYPE_SHIPPING);
            $originalAddress = $quote->getShippingAddress();

            if ($this->addressComparator->isEqual($newAddress, $originalAddress)) {
                unset($shippingData['shipping_address']);
            }
        }
    }

    /**
     * Prepare address
     *
     * @param Address $address
     * @param string $type
     * @return Address
     */
    private function prepareAddress($address, $type)
    {
        if (is_array($address)) {
            $address = $this->addressFactory
                ->create()
                ->setData($address)
                ->setAddressType($type);
        }

        return $address;
    }
}
