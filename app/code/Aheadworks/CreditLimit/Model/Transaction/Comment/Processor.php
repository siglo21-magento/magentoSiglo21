<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Comment;

use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterface;
use Aheadworks\CreditLimit\Model\Transaction\Comment\Metadata\CommentMetadataPool;
use Aheadworks\CreditLimit\Model\Transaction\Comment\Processor\ProcessorInterface;
use Magento\Framework\Phrase\Renderer\Placeholder;
use Aheadworks\CreditLimit\Model\Source\Transaction\Action as ActionSource;

/**
 * Class Processor
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Comment
 */
class Processor
{
    /**
     * @var CommentMetadataPool
     */
    private $commentMetadataPool;

    /**
     * @var Placeholder
     */
    private $placeholder;

    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @var ActionSource
     */
    protected $actionSource;

    /**
     * @param CommentMetadataPool $commentMetadataPool
     * @param Placeholder $placeholder
     * @param ActionSource $actionSource
     * @param array $processors
     */
    public function __construct(
        CommentMetadataPool $commentMetadataPool,
        Placeholder $placeholder,
        ActionSource $actionSource,
        array $processors
    ) {
        $this->commentMetadataPool = $commentMetadataPool;
        $this->placeholder = $placeholder;
        $this->actionSource = $actionSource;
        $this->processors = $processors;
    }

    /**
     * Retrieve comment placeholder
     *
     * @param string $commentType
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getPlaceholder($commentType)
    {
        if (in_array($commentType, $this->actionSource->getActionsWithCommentPlaceholders())) {
            return $this->commentMetadataPool->getMetadata($commentType)->getPlaceholder();
        }
        return '';
    }

    /**
     * Render comment
     *
     * @param string $commentType
     * @param TransactionEntityInterface[] $entities
     * @param bool $isUrl
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderComment($commentType, $entities, $isUrl)
    {
        if (in_array($commentType, $this->actionSource->getActionsWithCommentPlaceholders())) {
            $arguments = [];
            foreach ($this->processors as $processor) {
                $arguments = array_merge($arguments, $processor->renderComment($entities, $isUrl));
            }
            return $this->placeholder->render([$this->getPlaceholder($commentType)], $arguments);
        }
        return '';
    }
}
