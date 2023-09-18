<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api;

/**
 * Interface SellerCompanyManagementInterface
 * @api
 */
interface SellerCompanyManagementInterface
{
    /**
     * Create company
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyInterface $company
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Aheadworks\Ca\Api\Data\CompanyInterface
     */
    public function createCompany($company, $customer);

    /**
     * Update company
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyInterface $company
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Aheadworks\Ca\Api\Data\CompanyInterface
     */
    public function updateCompany($company, $customer);

    /**
     * Check if company blocked
     *
     * @param int $companyId
     * @return bool
     */
    public function isBlockedCompany($companyId);

    /**
     * Change company status
     *
     * @param int $companyId
     * @param string $status
     * @return bool
     */
    public function changeStatus($companyId, $status);

    /**
     * Retrieve company by customer id
     * @param int $customerId
     * @return \Aheadworks\Ca\Api\Data\CompanyInterface|null
     */
    public function getCompanyByCustomerId($customerId);
}
