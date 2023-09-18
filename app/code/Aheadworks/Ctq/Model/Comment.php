<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model;

use Aheadworks\Ctq\Api\Data\CommentInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Ctq\Model\ResourceModel\Comment as CommentResource;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Comment
 * @package Aheadworks\Ctq\Model
 */
class Comment extends AbstractModel implements CommentInterface
{
    /**
     * @var HistoryManagement
     */
    private $historyManagement;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param HistoryManagement $historyManagement
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        HistoryManagement $historyManagement,
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
        $this->historyManagement = $historyManagement;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(CommentResource::class);
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
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
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
    public function getAttachments()
    {
        return $this->getData(self::ATTACHMENTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttachments($attachments)
    {
        return $this->setData(self::ATTACHMENTS, $attachments);
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
        \Aheadworks\Ctq\Api\Data\CommentExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        if (!$this->getSkipHistory()) {
            $this->historyManagement->addCommentToHistory($this);
        }
        return $this;
    }
}
