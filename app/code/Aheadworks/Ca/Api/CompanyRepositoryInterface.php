<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api;

/**
 * Interface CompanyRepositoryInterface
 * @api
 */
interface CompanyRepositoryInterface
{
    /**
     * Save company
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyInterface $comapny
     * @return \Aheadworks\Ca\Api\Data\CompanyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Ca\Api\Data\CompanyInterface $company);

    /**
     * Retrieve company by id
     *
     * @param int $companyId
     * @return \Aheadworks\Ca\Api\Data\CompanyInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($companyId);

    /**
     * Retrieve company list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ca\Api\Data\CompanySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
