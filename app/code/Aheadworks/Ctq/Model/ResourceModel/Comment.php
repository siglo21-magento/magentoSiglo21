<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\ResourceModel;

use Aheadworks\Ctq\Api\Data\CommentInterface;

/**
 * Class Comment
 * @package Aheadworks\Ctq\Model\ResourceModel
 */
class Comment extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_ctq_comment';

    /**
     * Attachment table name
     */
    const ATTACHMENT_TABLE_NAME = 'aw_ctq_comment_attachment';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, CommentInterface::ID);
    }
}
