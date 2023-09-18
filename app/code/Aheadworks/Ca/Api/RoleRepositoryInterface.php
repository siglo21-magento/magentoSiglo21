<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api;

/**
 * Interface RoleRepositoryInterface
 * @api
 */
interface RoleRepositoryInterface
{
    /**
     * Save role
     *
     * @param \Aheadworks\Ca\Api\Data\RoleInterface $role
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Ca\Api\Data\RoleInterface $role);

    /**
     * Retrieve role by id
     *
     * @param int $roleId
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($roleId);

    /**
     * Retrieve role by id
     *
     * @param int $companyId
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDefaultUserRole($companyId);

    /**
     * Retrieve role list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ca\Api\Data\RoleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
