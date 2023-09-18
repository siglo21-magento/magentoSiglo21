<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface TransactionBuilderInterface
 *
 * @package Aheadworks\CreditLimit\Model\Transaction
 */
interface TransactionBuilderInterface
{
    /**
     * Check if provided parameters are valid for current builder
     *
     * @param TransactionParametersInterface $params
     * @return bool
     * @throws LocalizedException
     */
    public function checkIsValid(TransactionParametersInterface $params);

    /**
     * Fill up transaction object with data
     *
     * @param TransactionInterface $transaction
     * @param TransactionParametersInterface $params
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return bool
     */
    public function build(TransactionInterface $transaction, TransactionParametersInterface $params);
}
