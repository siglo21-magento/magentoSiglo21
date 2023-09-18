<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api;

/**
 * Interface CommentManagementInterface
 * @api
 */
interface CommentManagementInterface
{
    /**
     * Add new comment
     *
     * @param \Aheadworks\Ctq\Api\Data\CommentInterface $comment
     * @return \Aheadworks\Ctq\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addComment(\Aheadworks\Ctq\Api\Data\CommentInterface $comment);

    /**
     * Retrieve attachment
     *
     * @param string $fileName
     * @param int $commentId
     * @param int $quoteId
     * @return \Aheadworks\Ctq\Api\Data\CommentAttachmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttachment($fileName, $commentId, $quoteId);
}
