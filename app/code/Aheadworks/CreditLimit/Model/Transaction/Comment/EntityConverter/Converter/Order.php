<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter\Converter;

use Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter\ConverterInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterfaceFactory;
use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterface;
use Aheadworks\CreditLimit\Model\Source\Transaction\EntityType;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\DataObject;

/**
 * Class Order
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter\Converter
 */
class Order extends AbstractConverter implements ConverterInterface
{
    /**
     * Convert object to transaction entity
     *
     * @param DataObject|OrderInterface $order
     * @return TransactionEntityInterface
     */
    public function convertToTransactionEntity($order)
    {
        /** @var TransactionEntityInterface $transactionEntity */
        $transactionEntity = $this->transactionEntityFactory->create();
        $transactionEntity
            ->setEntityId($order->getIncrementId())
            ->setEntityLabel($order->getIncrementId())
            ->setEntityType(EntityType::ORDER_ID);

        return $transactionEntity;
    }
}
