<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ItemStatusRepositoryInterface
{

    /**
     * Save ItemStatus
     * @param \Aventi\SAP\Api\Data\ItemStatusInterface $itemStatus
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Aventi\SAP\Api\Data\ItemStatusInterface $itemStatus
    );

    /**
     * Retrieve ItemStatus
     * @param string $itemstatusId
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($itemstatusId);

    /**
     * Retrieve ItemStatus
     * @param string $itemId
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByItemId($itemId);

    /**
     * Retrieve ItemStatus matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aventi\SAP\Api\Data\ItemStatusSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ItemStatus
     * @param \Aventi\SAP\Api\Data\ItemStatusInterface $itemStatus
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Aventi\SAP\Api\Data\ItemStatusInterface $itemStatus
    );

    /**
     * Delete ItemStatus by ID
     * @param string $itemstatusId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($itemstatusId);
}
