<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Model\ResourceModel\Role as RoleResourceModel;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Ca\Model\Role\EntityProcessor;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Role
 * @package Aheadworks\Ca\Model
 */
class Role extends AbstractModel implements RoleInterface
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
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->processor = $processor;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(RoleResourceModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
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
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return $this->getData(self::PERMISSIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPermissions($permissions)
    {
        return $this->setData(self::PERMISSIONS, $permissions);
    }

    /**
     * @inheritDoc
     */
    public function isDefault()
    {
        return $this->getData(self::IS_DEFAULT);
    }

    /**
     * @inheritDoc
     */
    public function setIsDefault($isDefault)
    {
        return $this->setData(self::IS_DEFAULT, $isDefault);
    }

    /**
     * @inheritDoc
     */
    public function getCountUsers()
    {
        return $this->getData(self::COUNT_USERS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCountUsers($countUsers)
    {
        return $this->setData(self::COUNT_USERS, $countUsers);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwStcBaseAmountLimit()
    {
        return $this->getData(self::AW_STC_BASE_AMOUNT_LIMIT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwStcBaseAmountLimit($amount)
    {
        return $this->setData(self::AW_STC_BASE_AMOUNT_LIMIT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwRpBaseAmountLimit()
    {
        return $this->getData(self::AW_RP_BASE_AMOUNT_LIMIT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwRpBaseAmountLimit($amount)
    {
        return $this->setData(self::AW_RP_BASE_AMOUNT_LIMIT, $amount);
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
    public function setExtensionAttributes(
        \Aheadworks\Ca\Api\Data\RoleExtensionInterface $extensionAttributes
    ) {
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
