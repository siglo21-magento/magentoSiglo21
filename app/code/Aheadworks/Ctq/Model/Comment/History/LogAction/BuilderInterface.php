<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Comment\History\LogAction;

use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Model\Comment;

/**
 * Interface BuilderInterface
 * @package Aheadworks\Ctq\Model\Comment\History\LogAction
 */
interface BuilderInterface
{
    /**
     * Build history action from comment object
     *
     * @param CommentInterface|Comment $comment
     * @return HistoryActionInterface[]
     */
    public function build($comment);
}
