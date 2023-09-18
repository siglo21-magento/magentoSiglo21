<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Builder;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Model\Transaction\TransactionBuilderInterface;
use Aheadworks\CreditLimit\Model\Source\Transaction\Action as TransactionActionSource;
use Aheadworks\CreditLimit\Model\Transaction\CreditSummaryManagement;
use Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter;
use Aheadworks\CreditLimit\Model\Transaction\Comment\Processor as CommentProcessor;

/**
 * Class CommentToCustomer
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Builder
 */
class CommentToCustomer extends AbstractBuilder implements TransactionBuilderInterface
{
    /**
     * @var EntityConverter
     */
    private $entityConverter;

    /**
     * @var CommentProcessor
     */
    private $commentProcessor;

    /**
     * @param TransactionActionSource $transactionActionSource
     * @param CreditSummaryManagement $summaryManagement
     * @param EntityConverter $entityConverter
     * @param CommentProcessor $commentProcessor
     */
    public function __construct(
        TransactionActionSource $transactionActionSource,
        CreditSummaryManagement $summaryManagement,
        EntityConverter $entityConverter,
        CommentProcessor $commentProcessor
    ) {
        parent::__construct($transactionActionSource, $summaryManagement);
        $this->entityConverter = $entityConverter;
        $this->commentProcessor = $commentProcessor;
    }

    /**
     * @inheritdoc
     */
    public function checkIsValid(TransactionParametersInterface $params)
    {
        $result = true;
        if (!$params->getCommentToCustomer()
            && !$params->getOrderEntity()
            && !$params->getCreditmemoEntity()) {
            $result = false;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function build(TransactionInterface $transaction, TransactionParametersInterface $params)
    {
        $commentToCustomer = $params->getCommentToCustomer();
        $entities = $this->prepareEntities($params);
        if (!$commentToCustomer && !empty($entities)) {
            $transactionEntities = $this->entityConverter->convert($entities);
            $customerCommentPlaceholder = $this->commentProcessor->getPlaceholder($params->getAction());
            $commentToCustomer = $this->commentProcessor->renderComment(
                $params->getAction(),
                $transactionEntities,
                false
            );
            $transaction->setCommentToCustomerPlaceholder($customerCommentPlaceholder);
            $transaction->setEntities($transactionEntities);
        }

        $transaction->setCommentToCustomer($commentToCustomer);
    }

    /**
     * Prepare entities
     *
     * @param TransactionParametersInterface $params
     * @return array
     */
    private function prepareEntities(TransactionParametersInterface $params)
    {
        $entities = [];
        if ($params->getOrderEntity()) {
            $entities[] = $params->getOrderEntity();
        }
        if ($params->getCreditmemoEntity()) {
            $entities[] = $params->getCreditmemoEntity();
        }

        return $entities;
    }
}
