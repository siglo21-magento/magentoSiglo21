<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\ResourceModel\Comment\Relation\Attachment;

use Aheadworks\Ctq\Api\Data\CommentInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Ctq\Api\Data\CommentAttachmentInterface;
use Aheadworks\Ctq\Api\Data\CommentAttachmentInterfaceFactory;

/**
 * Class ReadHandler
 * @package Aheadworks\Ctq\Model\ResourceModel\Comment\Relation\Attachment
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var CommentAttachmentInterfaceFactory
     */
    private $commentAttachmentFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param CommentAttachmentInterfaceFactory $commentAttachmentFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        CommentAttachmentInterfaceFactory $commentAttachmentFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->commentAttachmentFactory = $commentAttachmentFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entityId = (int)$entity->getId()) {
            $connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(CommentInterface::class)->getEntityConnectionName()
            );
            $select = $connection->select()
                ->from($this->resourceConnection->getTableName('aw_ctq_comment_attachment'))
                ->where('comment_id = :id');
            $attachmentsData = $connection->fetchAll($select, ['id' => $entityId]);

            $attachments = [];
            foreach ($attachmentsData as $attachmentData) {
                $commentAttachmentEntity = $this->commentAttachmentFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $commentAttachmentEntity,
                    $attachmentData,
                    CommentAttachmentInterface::class
                );
                $attachments[] = $commentAttachmentEntity;
            }
            $entity->setAttachments($attachments);
        }
        return $entity;
    }
}
