<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction;

use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CompositeBuilder
 *
 * @package Aheadworks\CreditLimit\Model\Transaction
 */
class CompositeBuilder
{
    /**
     * @var TransactionInterfaceFactory
     */
    private $transactionFactory;

    /**
     * @var TransactionBuilderInterface[]
     */
    private $builders;

    /**
     * @param TransactionInterfaceFactory $transactionFactory
     * @param TransactionBuilderInterface[] $builders
     */
    public function __construct(
        TransactionInterfaceFactory $transactionFactory,
        $builders = []
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->builders = $builders;
    }

    /**
     * Build transaction using provided params
     *
     * @param TransactionParametersInterface $params
     * @return TransactionInterface
     * @throws LocalizedException
     */
    public function build(TransactionParametersInterface $params)
    {
        /** @var TransactionInterface $transaction */
        $transaction = $this->transactionFactory->create();
        foreach ($this->builders as $builder) {
            if ($builder->checkIsValid($params)) {
                $builder->build($transaction, $params);
            }
        }

        return $transaction;
    }
}
