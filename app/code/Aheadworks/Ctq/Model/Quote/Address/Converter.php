<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Address;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Ui\Component\Form\Element\Multiline;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Converter
 *
 * @package Aheadworks\Ctq\Model\Quote\Address
 */
class Converter
{
    /**
     * @var AddressMetadataInterface
     */
    private $addressMetadata;

    /**
     * @param AddressMetadataInterface $addressMetadata
     */
    public function __construct(
        AddressMetadataInterface $addressMetadata
    ) {
        $this->addressMetadata = $addressMetadata;
    }

    /**
     * Convert address to array
     *
     * @param AddressInterface $address
     * @return array
     * @throws LocalizedException
     */
    public function convertToArray(AddressInterface $address)
    {
        return $this->convertAddress($address, true);
    }

    /**
     * Convert address data appropriate to fill checkout address form
     *
     * @param AddressInterface $address
     * @return array
     * @throws LocalizedException
     */
    public function convertForCheckoutForm(AddressInterface $address)
    {
        return $this->convertAddress($address);
    }

    /**
     * Convert address
     *
     * @param AddressInterface $address
     * @param bool $isArrayOutput
     * @return array
     * @throws LocalizedException
     */
    private function convertAddress(AddressInterface $address, $isArrayOutput = false)
    {
        $addressData = [];
        $attributesMetadata = $this->addressMetadata->getAllAttributesMetadata();
        foreach ($attributesMetadata as $attributeMetadata) {
            if (!$attributeMetadata->isVisible()) {
                continue;
            }
            $attributeCode = $attributeMetadata->getAttributeCode();
            $attributeData = $address->getData($attributeCode);
            if ($attributeData) {
                if ($attributeMetadata->getFrontendInput() === Multiline::NAME) {
                    $attributeData = is_array($attributeData) ? $attributeData : explode("\n", $attributeData);
                    if (!$isArrayOutput) {
                        $attributeData = (object)$attributeData;
                    }
                }
                if ($attributeMetadata->isUserDefined()) {
                    $addressData[CustomAttributesDataInterface::CUSTOM_ATTRIBUTES][$attributeCode] = $attributeData;
                    continue;
                }
                $addressData[$attributeCode] = $attributeData;
            }
        }
        return $addressData;
    }
}
