<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Customer;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\EntityProcessor;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser as CompanyUserResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class CompanyUser
 * @package Aheadworks\Ca\Model\Customer
 */
class CompanyUser extends AbstractModel implements CompanyUserInterface
{
    /**
     * @var EntityProcessor
     */
    private $processor;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param EntityProcessor $processor
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        EntityProcessor $processor,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->processor = $processor;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(CompanyUserResourceModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompanyId()
    {
        return $this->getData(self::COMPANY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCompanyId($companyId)
    {
        return $this->setData(self::COMPANY_ID, $companyId);
    }

    /**
     * @inheritDoc
     */
    public function getIsRoot()
    {
        return $this->getData(self::IS_ROOT);
    }

    /**
     * @inheritDoc
     */
    public function setIsRoot($isRoot)
    {
        return $this->setData(self::IS_ROOT, $isRoot);
    }

    /**
     * @inheritDoc
     */
    public function getIsActivated()
    {
        return $this->getData(self::IS_ACTIVATED);
    }

    /**
     * @inheritDoc
     */
    public function setIsActivated($isActivated)
    {
        return $this->setData(self::IS_ACTIVATED, $isActivated);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompanyGroupId()
    {
        return $this->getData(self::COMPANY_GROUP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCompanyGroupId($groupId)
    {
        return $this->setData(self::COMPANY_GROUP_ID, $groupId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompanyRoleId()
    {
        return $this->getData(self::COMPANY_ROLE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCompanyRoleId($roleId)
    {
        return $this->setData(self::COMPANY_ROLE_ID, $roleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getJobTitle()
    {
        return $this->getData(self::JOB_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setJobTitle($jobTitle)
    {
        return $this->setData(self::JOB_TITLE, $jobTitle);
    }

    /**
     * @inheritDoc
     */
    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE);
    }

    /**
     * @inheritDoc
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(\Aheadworks\Ca\Api\Data\CompanyUserExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $this->processor->prepareDataBeforeSave($this);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function afterLoad()
    {
        $this->processor->prepareDataAfterLoad($this);
        return $this;
    }
}
