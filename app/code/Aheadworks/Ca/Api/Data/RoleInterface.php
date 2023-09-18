<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface RoleInterface
 * @api
 */
interface RoleInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const COMPANY_ID = 'company_id';
    const NAME = 'name';
    const PERMISSIONS = 'permissions';
    const COUNT_USERS = 'count_users';
    const IS_DEFAULT = 'default';
    const AW_STC_BASE_AMOUNT_LIMIT = 'aw_stc_base_amount_limit';
    const AW_RP_BASE_AMOUNT_LIMIT = 'aw_rp_base_amount_limit';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int|null $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get company id
     *
     * @return int
     */
    public function getCompanyId();

    /**
     * Set company id
     *
     * @param int|null $companyId
     * @return $this
     */
    public function setCompanyId($companyId);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get permissions
     *
     * @return \Aheadworks\Ca\Api\Data\RolePermissionInterface[]
     */
    public function getPermissions();

    /**
     * Set permissions
     *
     * @param \Aheadworks\Ca\Api\Data\RolePermissionInterface[] $permissions
     * @return $this
     */
    public function setPermissions($permissions);

    /**
     * Is default
     *
     * @return bool
     */
    public function isDefault();

    /**
     * Set is default
     *
     * @param bool $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault);

    /**
     * Get count users
     *
     * @return int
     */
    public function getCountUsers();

    /**
     * Set count users
     *
     * @param int $countUsers
     * @return $this
     */
    public function setCountUsers($countUsers);

    /**
     * Get aw stc base amount limit
     *
     * @return float
     */
    public function getAwStcBaseAmountLimit();

    /**
     * Set aw stc base amount limit
     *
     * @param float $amount
     * @return $this
     */
    public function setAwStcBaseAmountLimit($amount);

    /**
     * Get aw reward points base amount limit
     *
     * @return float
     */
    public function getAwRpBaseAmountLimit();

    /**
     * Set aw reward points base amount limit
     *
     * @param float $amount
     * @return $this
     */
    public function setAwRpBaseAmountLimit($amount);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ca\Api\Data\RoleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ca\Api\Data\RoleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ca\Api\Data\RoleExtensionInterface $extensionAttributes
    );
}
