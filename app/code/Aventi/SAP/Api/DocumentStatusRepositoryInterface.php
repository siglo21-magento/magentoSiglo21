<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface DocumentStatusRepositoryInterface
{

    /**
     * Save DocumentStatus
     * @param \Aventi\SAP\Api\Data\DocumentStatusInterface $documentStatus
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Aventi\SAP\Api\Data\DocumentStatusInterface $documentStatus
    );

    /**
     * Retrieve DocumentStatus
     * @param string $documentstatusId
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($documentstatusId);

    /**
     * Retrieve DocumentStatus
     * @param string $parentId
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByParentId($parentId);

    /**
     * Retrieve DocumentStatus matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aventi\SAP\Api\Data\DocumentStatusSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete DocumentStatus
     * @param \Aventi\SAP\Api\Data\DocumentStatusInterface $documentStatus
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Aventi\SAP\Api\Data\DocumentStatusInterface $documentStatus
    );

    /**
     * Delete DocumentStatus by ID
     * @param string $documentstatusId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($documentstatusId);
}

