<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Api;

/**
 * Interface TransactionManagementInterface
 * @api
 */
interface TransactionManagementInterface
{
    /**
     * Create transaction
     *
     * List of params:
     * customer_id - required
     * action - required
     * amount - depends on action
     * amount_currency - depends on action
     * used_currency - depends on action
     * credit_limit - depends on action
     * other params are optional
     *
     * @param \Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface $params
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createTransaction(\Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface $params);
}
