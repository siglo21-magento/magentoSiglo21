<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceList\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PriceListRepositoryInterface
{

    /**
     * Save PriceList
     * @param \Aventi\PriceList\Api\Data\PriceListInterface $priceList
     * @return \Aventi\PriceList\Api\Data\PriceListInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Aventi\PriceList\Api\Data\PriceListInterface $priceList
    );

    /**
     * Retrieve PriceList
     * @param string $pricelistId
     * @return \Aventi\PriceList\Api\Data\PriceListInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($pricelistId);

    /**
     * Retrieve PriceList matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aventi\PriceList\Api\Data\PriceListSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete PriceList
     * @param \Aventi\PriceList\Api\Data\PriceListInterface $priceList
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Aventi\PriceList\Api\Data\PriceListInterface $priceList
    );

    /**
     * Delete PriceList by ID
     * @param string $pricelistId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($pricelistId);
}

