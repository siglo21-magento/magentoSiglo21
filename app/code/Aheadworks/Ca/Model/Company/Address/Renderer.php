<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Company\Address;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Customer\Block\Address\Renderer\RendererInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Magento\Directory\Model\RegionFactory;

/**
 * Class Renderer
 *
 * @package Aheadworks\Ca\Model\Company\Address
 */
class Renderer
{
    /**
     * @var AddressConfig
     */
    private $addressConfig;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private $extensibleDataObjectConverter;

    /**
     * @param AddressConfig $addressConfig
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param AddressInterfaceFactory $addressFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        AddressConfig $addressConfig,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        AddressInterfaceFactory $addressFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        RegionFactory $regionFactory
    ) {
        $this->addressConfig = $addressConfig;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->addressFactory = $addressFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->regionFactory = $regionFactory;
    }

    /**
     * Render customer address
     *
     * @param AddressInterface $address
     * @return string
     */
    public function render($address)
    {
        $formatType = $this->addressConfig->getFormatByCode('html');
        if (!$formatType || !$formatType->getRenderer()) {
            return null;
        }

        /** @var RendererInterface $renderer */
        $renderer = $formatType->getRenderer();
        $flatAddressArray = $this->convertAddressToArray($address);

        return empty($flatAddressArray) ? '' : $renderer->renderArray($flatAddressArray);
    }

    /**
     * Render address from company
     *
     * @param CompanyInterface $company
     * @return string
     */
    public function renderAddressFromCompany($company)
    {
        /** @var AddressInterface $company */
        $address = $this->addressFactory->create();
        $addressData = $this->dataObjectProcessor->buildOutputDataArray($company, CompanyInterface::class);
        $addressData[AddressInterface::STREET] = [
            $addressData[AddressInterface::STREET]
        ];
        $addressData[AddressInterface::REGION] = $this->prepareRegion($addressData);

        $this->dataObjectHelper->populateWithArray(
            $address,
            $addressData,
            AddressInterface::class
        );

        return $this->render($address);
    }

    /**
     * Convert address to flat array
     *
     * @param AddressInterface $address
     * @return array
     */
    private function convertAddressToArray($address)
    {
        $flatAddressArray = $this->extensibleDataObjectConverter->toFlatArray(
            $address,
            [],
            AddressInterface::class
        );

        return $this->prepareAddressData($flatAddressArray, $address);
    }

    /**
     * Prepare address data
     *
     * @param array $flatAddressArray
     * @param AddressInterface $address
     * @return mixed
     */
    private function prepareAddressData($flatAddressArray, $address)
    {
        $street = $address->getStreet();
        if (!empty($street) && is_array($street)) {
            $streetKeys = array_keys($street);
            foreach ($streetKeys as $key) {
                unset($flatAddressArray[$key]);
            }
            $flatAddressArray[AddressInterface::STREET] = $street;
        }

        return $flatAddressArray;
    }

    /**
     * Prepare region data
     *
     * @param array $addressData
     * @return array
     */
    private function prepareRegion($addressData)
    {
        $regionData = [
            RegionInterface::REGION_ID => $addressData[AddressInterface::REGION_ID]
        ];

        if ($addressData[AddressInterface::REGION_ID]) {
            $region = $this->regionFactory->create()->load($addressData[AddressInterface::REGION_ID]);
            $regionData[RegionInterface::REGION] = $region->getName();
        } else {
            $regionData[RegionInterface::REGION] = $addressData[AddressInterface::REGION];
        }

        return $regionData;
    }
}
