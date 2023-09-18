<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Website;

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Website;

/**
 * Class CurrencyList
 *
 * @package Aheadworks\CreditLimit\Model\Website
 */
class CurrencyList
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StoreCollectionFactory
     */
    private $storeCollectionFactory;

    /**
     * @param StoreManagerInterface $storeManager
     * @param StoreCollectionFactory $storeCollectionFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        StoreCollectionFactory $storeCollectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->storeCollectionFactory = $storeCollectionFactory;
    }

    /**
     * Get all allowed currencies for website
     *
     * @param int $websiteId
     * @return array
     */
    public function getAllowedCurrenciesForWebsite($websiteId)
    {
        $allowedCurrencies = [];
        $storeCollection = $this->storeCollectionFactory->create();
        $storeCollection->addWebsiteFilter($websiteId);
        $stores = $storeCollection->getItems();
        /** @var Store $store */
        foreach ($stores as $store) {
            $allowedCurrencies = array_merge(
                $allowedCurrencies,
                $store->getAvailableCurrencyCodes(true)
            );
        }

        return array_unique($allowedCurrencies);
    }

    /**
     * Get base currency for website
     *
     * @param int $websiteId
     * @return string
     * @throws LocalizedException
     */
    public function getBaseCurrencyForWebsite($websiteId)
    {
        /** @var Website $website */
        $website = $this->storeManager->getWebsite($websiteId);
        return $website->getBaseCurrencyCode();
    }
}
