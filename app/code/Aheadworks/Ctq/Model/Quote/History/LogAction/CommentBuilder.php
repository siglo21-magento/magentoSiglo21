<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\History\LogAction;

use Aheadworks\Ctq\Model\Comment\ToHistory as CommentToHistory;

/**
 * Class CommentBuilder
 * @package Aheadworks\Ctq\Model\Quote\History\LogAction
 */
class CommentBuilder implements BuilderInterface
{
    /**
     * @var CommentToHistory
     */
    private $commentToHistory;

    /**
     * @param CommentToHistory $commentToHistory
     */
    public function __construct(
        CommentToHistory $commentToHistory
    ) {
        $this->commentToHistory = $commentToHistory;
    }

    /**
     * {@inheritdoc}
     */
    public function build($quote)
    {
        $historyActions = [];
        if ($comment = $quote->getComment()) {
            $commentHistory = $this->commentToHistory->convert($comment);

            $historyActions = $commentHistory->getActions();
        }

        return $historyActions;
    }
}
