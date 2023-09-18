<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Source\Company;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Country
 * @package Aheadworks\Ca\Model\Source\Company
 */
class Country implements OptionSourceInterface
{
    /**
     * @var CountryCollectionFactory
     */
    private $countryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @inheritDoc
     */
    public function __construct(
        CountryCollectionFactory $countryCollection,
        StoreManagerInterface $storeManager
    ) {
        $this->countryCollectionFactory = $countryCollection;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     * @throws NoSuchEntityException
     */
    public function toOptionArray()
    {
        return $this->countryCollectionFactory->create()
            ->loadByStore($this->storeManager->getStore()->getId())
            ->toOptionArray();
    }
}
