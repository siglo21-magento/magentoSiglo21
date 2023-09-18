<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Builder;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Model\Transaction\TransactionBuilderInterface;

/**
 * Class PurchaseOrder
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Builder
 */
class PurchaseOrder extends AbstractBuilder implements TransactionBuilderInterface
{
    /**
     * @inheritdoc
     */
    public function checkIsValid(TransactionParametersInterface $params)
    {
        return $params->getPoNumber() !== null;
    }

    /**
     * @inheritdoc
     */
    public function build(TransactionInterface $transaction, TransactionParametersInterface $params)
    {
        $transaction->setPoNumber($params->getPoNumber());
    }
}
