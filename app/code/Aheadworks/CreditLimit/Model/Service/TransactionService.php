<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Service;

use Aheadworks\CreditLimit\Api\TransactionManagementInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\TransactionRepositoryInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction as TransactionResource;
use Aheadworks\CreditLimit\Model\Transaction\CompositeBuilder as TransactionCompositeBuilder;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Model\Customer\Notifier;

/**
 * Class TransactionService
 *
 * @package Aheadworks\CreditLimit\Model\Service
 */
class TransactionService implements TransactionManagementInterface
{
    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var TransactionResource
     */
    private $transactionResource;

    /**
     * @var TransactionCompositeBuilder
     */
    private $transactionCompositeBuilder;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @param TransactionRepositoryInterface $transactionRepository
     * @param TransactionResource $transactionResource
     * @param TransactionCompositeBuilder $transactionCompositeBuilder
     * @param Notifier $notifier
     */
    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        TransactionResource $transactionResource,
        TransactionCompositeBuilder $transactionCompositeBuilder,
        Notifier $notifier
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionResource = $transactionResource;
        $this->transactionCompositeBuilder = $transactionCompositeBuilder;
        $this->notifier = $notifier;
    }

    /**
     * @inheritdoc
     */
    public function createTransaction(TransactionParametersInterface $params)
    {
        try {
            $this->transactionResource->beginTransaction();
            $transaction = $this->transactionCompositeBuilder->build($params);
            $this->transactionRepository->save($transaction);
            $this->transactionResource->commit();
            $this->notifier->notify($params->getCustomerId(), $transaction);
        } catch (\Exception $e) {
            $this->transactionResource->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }

        return $transaction;
    }
}
