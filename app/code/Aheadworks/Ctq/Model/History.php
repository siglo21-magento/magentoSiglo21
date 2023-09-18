<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model;

use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Ctq\Model\ResourceModel\History as HistoryResource;
use Aheadworks\Ctq\Model\History\EntityProcessor;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class History
 * @package Aheadworks\Ctq\Model
 */
class History extends AbstractModel implements HistoryInterface
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
        $this->_init(HistoryResource::class);
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
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($actionType)
    {
        return $this->setData(self::STATUS, $actionType);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnerType()
    {
        return $this->getData(self::OWNER_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setOwnerType($ownerType)
    {
        return $this->setData(self::OWNER_TYPE, $ownerType);
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnerName()
    {
        return $this->getData(self::OWNER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setOwnerName($ownerName)
    {
        return $this->setData(self::OWNER_NAME, $ownerName);
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnerId()
    {
        return $this->getData(self::OWNER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOwnerId($ownerId)
    {
        return $this->setData(self::OWNER_ID, $ownerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        return $this->getData(self::ACTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setActions($actions)
    {
        return $this->setData(self::ACTIONS, $actions);
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
        \Aheadworks\Ctq\Api\Data\HistoryExtensionInterface $extensionAttributes
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
    public function afterSave()
    {
        $this->processor->prepareDataAfterLoad($this);
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
