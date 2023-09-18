<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Customer\Notifier;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Model\Email\EmailMetadataInterface;

/**
 * Interface EmailProcessorInterface
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Notifier
 */
interface EmailProcessorInterface
{
    /**
     * Process email
     *
     * @param int $customerId
     * @param TransactionInterface $transaction
     * @return EmailMetadataInterface|bool
     */
    public function process($customerId, $transaction);
}
