<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api;

/**
 * Interface RoleManagementInterface
 * @api
 */
interface RoleManagementInterface
{
    /**
     * Create default role
     *
     * @param int $companyId
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createDefaultRole($companyId);

    /**
     * Create default user role
     *
     * @param int $companyId
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createDefaultUserRole($companyId);

    /**
     * Save role
     *
     * @param \Aheadworks\Ca\Api\Data\RoleInterface $role
     * @param string[] $postedResources
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveRole($role, $postedResources);
}
