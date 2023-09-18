<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Comment\Processor;

use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterface;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Comment\Processor
 */
interface ProcessorInterface
{
    /**
     * Render comment
     *
     * @param TransactionEntityInterface[] $entities
     * @param bool $isUrl
     * @return string
     */
    public function renderComment($entities, $isUrl);
}
