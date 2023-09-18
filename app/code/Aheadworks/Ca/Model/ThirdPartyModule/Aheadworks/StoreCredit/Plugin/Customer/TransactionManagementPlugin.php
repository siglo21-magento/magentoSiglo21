<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\SummaryManagement;

/**
 * Class TransactionManagementPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Customer
 */
class TransactionManagementPlugin
{
    /**
     * @var SummaryManagement
     */
    private $summaryManagement;

    /**
     * @param SummaryManagement $summaryManagement
     */
    public function __construct(
        SummaryManagement $summaryManagement
    ) {
        $this->summaryManagement = $summaryManagement;
    }

    /**
     * Adjust transaction balance for company user customer
     *
     * @param \Aheadworks\StoreCredit\Api\TransactionManagementInterface $subject
     * @param int $transactionId
     * @param float $balance
     * @return array
     */
    public function beforeUpdateCurrentBalance($subject, $transactionId, $balance)
    {
        $balance = $this->summaryManagement->getCurrentCustomerBalanceByTransactionId($transactionId, $balance);
        return [$transactionId, $balance];
    }
}
