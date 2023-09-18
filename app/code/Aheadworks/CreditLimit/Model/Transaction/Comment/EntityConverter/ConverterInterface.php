<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter;

use Magento\Framework\DataObject;
use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterface;

/**
 * Interface ConverterInterface
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter
 */
interface ConverterInterface
{
    /**
     * Convert object to transaction entity
     *
     * @param DataObject $object
     * @return TransactionEntityInterface
     */
    public function convertToTransactionEntity($object);
}
