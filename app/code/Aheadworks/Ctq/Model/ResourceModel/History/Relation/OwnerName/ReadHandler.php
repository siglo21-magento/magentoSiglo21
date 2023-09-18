<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\ResourceModel\History\Relation\OwnerName;

use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Model\ResourceModel\Owner\Relation\OwnerName\AbstractReadHandler;

/**
 * Class ReadHandler
 * @package Aheadworks\Ctq\Model\ResourceModel\History\Relation\OwnerName
 */
class ReadHandler extends AbstractReadHandler
{
    /**
     * {@inheritdoc}
     */
    protected function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(HistoryInterface::class)->getEntityConnectionName()
        );
    }
}
