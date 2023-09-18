<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Ui\Component\Listing\Transaction\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Aheadworks\CreditLimit\Model\Source\Transaction\Action as ActionSource;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Model\Transaction\Comment\Processor as CommentProcessor;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter;

/**
 * Class CommentToCustomer
 *
 * @package Aheadworks\CreditLimit\Ui\Component\Listing\Transaction\Columns
 */
class CommentToCustomer extends Column
{
    /**
     * @var ActionSource
     */
    private $actionSource;

    /**
     * @var CommentProcessor
     */
    private $commentProcessor;

    /**
     * @var EntityConverter
     */
    private $entityConverter;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CommentProcessor $commentProcessor
     * @param ActionSource $actionSource
     * @param EntityConverter $entityConverter
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CommentProcessor $commentProcessor,
        ActionSource $actionSource,
        EntityConverter $entityConverter,
        array $components = [],
        array $data = []
    ) {
        $this->commentProcessor = $commentProcessor;
        $this->actionSource = $actionSource;
        $this->entityConverter = $entityConverter;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (in_array($item['action'], $this->actionSource->getActionsWithCommentPlaceholders())) {
                    $comment = $this->prepareCommentForEntities(
                        $item[TransactionInterface::ACTION],
                        $item[TransactionInterface::ENTITIES]
                    );
                } else {
                    $comment = $item[TransactionInterface::COMMENT_TO_CUSTOMER];
                }

                $item[$this->getData('name')] = $comment;
            }
            return $dataSource;
        }
    }

    /**
     * Prepare comment using entities
     *
     * @param string $action
     * @param array $entities
     * @return string
     */
    private function prepareCommentForEntities($action, $entities)
    {
        $entityObjects = $this->entityConverter->convertFromArrayToObject($entities);
        try {
            $comment = $this->commentProcessor->renderComment($action, $entityObjects, true);
        } catch (\Exception $exception) {
            $comment = '';
        }

        return $comment;
    }
}
