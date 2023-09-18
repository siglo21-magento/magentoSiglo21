<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CompanyUserInterface
 * @package Aheadworks\Ca\Api\Data
 */
interface CompanyUserInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const CUSTOMER_ID = 'customer_id';
    const COMPANY_ID = 'company_id';
    const IS_ROOT = 'is_root';
    const IS_ACTIVATED = 'is_activated';
    const COMPANY_GROUP_ID = 'company_group_id';
    const COMPANY_ROLE_ID = 'company_role_id';
    const JOB_TITLE = 'job_title';
    const TELEPHONE = 'telephone';
    /**#@-*/

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get company id
     *
     * @return int
     */
    public function getCompanyId();

    /**
     * Set company id
     *
     * @param int $companyId
     * @return $this
     */
    public function setCompanyId($companyId);

    /**
     * Get is root flag
     *
     * @return boolean
     */
    public function getIsRoot();

    /**
     * Set is root flag
     *
     * @param boolean $isRoot
     * @return $this
     */
    public function setIsRoot($isRoot);

    /**
     * Get is activated flag
     *
     * @return boolean
     */
    public function getIsActivated();

    /**
     * Set is activated flag
     *
     * @param boolean $isActivated
     * @return $this
     */
    public function setIsActivated($isActivated);

    /**
     * Get company group id
     *
     * @return int
     */
    public function getCompanyGroupId();

    /**
     * Set company group id
     *
     * @param int $groupId
     * @return $this
     */
    public function setCompanyGroupId($groupId);

    /**
     * Get company role id
     *
     * @return int
     */
    public function getCompanyRoleId();

    /**
     * Set company role id
     *
     * @param int $roleId
     * @return $this
     */
    public function setCompanyRoleId($roleId);

    /**
     * Get job title
     *
     * @return string
     */
    public function getJobTitle();

    /**
     * Set job title
     *
     * @param string $jobTitle
     * @return $this
     */
    public function setJobTitle($jobTitle);

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone();

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ca\Api\Data\CompanyUserExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyUserExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ca\Api\Data\CompanyUserExtensionInterface $extensionAttributes
    );
}
