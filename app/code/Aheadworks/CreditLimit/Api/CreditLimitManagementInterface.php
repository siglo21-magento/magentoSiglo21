<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Api;

/**
 * Interface CreditLimitManagementInterface
 * @api
 */
interface CreditLimitManagementInterface
{
    /**
     * Update credit limit amount for specified customer.
     *
     * Custom credit limit will be configured for specified customer
     *
     * @param int $customerId
     * @param float $creditLimit
     * @param string $commentToAdmin
     * @param string $commentToCustomer
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateCreditLimit(
        $customerId,
        $creditLimit,
        $commentToAdmin = '',
        $commentToCustomer = ''
    );

    /**
     * Update default credit limit amount for specified customer.
     *
     * This method is used to create transaction for customer with
     * credit limit specified for customer group. Custom credit limit will be reset.
     *
     * @param int $customerId
     * @param float $creditLimit
     * @param string $commentToAdmin
     * @param string $commentToCustomer
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateDefaultCreditLimit(
        $customerId,
        $creditLimit,
        $commentToAdmin = '',
        $commentToCustomer = ''
    );

    /**
     * Update credit limit amount for specified customer
     *
     * @param int $customerId
     * @param float $amount
     * @param string|null $currency
     * @param string $commentToAdmin
     * @param string $commentToCustomer
     * @param string $poNumber
     * @return boolean
     */
    public function updateCreditBalance(
        $customerId,
        $amount,
        $currency = null,
        $commentToAdmin = '',
        $commentToCustomer = '',
        $poNumber = ''
    );

    /**
     * Spend customer credit balance on order
     *
     * @param int $customerId
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return boolean
     */
    public function spendCreditBalanceOnOrder($customerId, $order);

    /**
     * Reimburse customer credit balance on cancelled order
     *
     * @param int $customerId
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return boolean
     */
    public function reimburseCreditBalanceOnCanceledOrder($customerId, $order);

    /**
     * Refund credit balance on creditmemo
     *
     * @param int $customerId
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @return boolean
     */
    public function refundCreditBalanceOnCreditmemo($customerId, $order, $creditmemo);
}
