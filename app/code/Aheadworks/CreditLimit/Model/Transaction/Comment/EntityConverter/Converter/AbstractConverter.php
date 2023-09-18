<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter\Converter;

use Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter\ConverterInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterfaceFactory;

/**
 * Class Creditmemo
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter\Converter
 */
abstract class AbstractConverter implements ConverterInterface
{
    /**
     * @var TransactionEntityInterfaceFactory
     */
    protected $transactionEntityFactory;

    /**
     * @param TransactionEntityInterfaceFactory $transactionEntityFactory
     */
    public function __construct(
        TransactionEntityInterfaceFactory $transactionEntityFactory
    ) {
        $this->transactionEntityFactory = $transactionEntityFactory;
    }

    /**
     * @inheritdoc
     */
    abstract public function convertToTransactionEntity($object);
}
