<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\StoreCreditManagement;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class StoreCreditServicePlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Customer
 */
class StoreCreditServicePlugin
{
    /**
     * @var StoreCreditManagement
     */
    private $storeCreditManagement;

    /**
     * @param StoreCreditManagement $storeCreditManagement
     */
    public function __construct(
        StoreCreditManagement $storeCreditManagement
    ) {
        $this->storeCreditManagement = $storeCreditManagement;
    }

    /**
     * Spend customer store credit on checkout
     *
     * @param \Aheadworks\StoreCredit\Model\Service\CustomerStoreCreditService $subject
     * @param int $customerId
     * @param int $spendStoreCredit
     * @param OrderInterface $order
     * @param int $websiteId
     * @return array
     */
    public function beforeSpendStoreCreditOnCheckout($subject, $customerId, $spendStoreCredit, $order, $websiteId)
    {
        $customerId = $this->storeCreditManagement->changeCustomerIdIfNeeded($customerId);

        return [$customerId, $spendStoreCredit, $order, $websiteId];
    }

    /**
     * Refund to store credit
     *
     * @param \Aheadworks\StoreCredit\Model\Service\CustomerStoreCreditService $subject
     * @param int $customerId
     * @param int $refundToStoreCredit
     * @param int $orderId
     * @param CreditmemoInterface $creditmemo
     * @param int $websiteId
     * @return array
     */
    public function beforeRefundToStoreCredit(
        $subject,
        $customerId,
        $refundToStoreCredit,
        $orderId,
        $creditmemo,
        $websiteId
    ) {
        $customerId = $this->storeCreditManagement->changeCustomerIdIfNeeded($customerId);

        return [$customerId, $refundToStoreCredit, $orderId, $creditmemo, $websiteId];
    }

    /**
     * Reimbursed spent store credit
     *
     * @param \Aheadworks\StoreCredit\Model\Service\CustomerStoreCreditService $subject
     * @param int $customerId
     * @param int $refundToStoreCredit
     * @param int $orderId
     * @param CreditmemoInterface $creditmemo
     * @param int $websiteId
     * @return array
     */
    public function beforeReimbursedSpentStoreCredit(
        $subject,
        $customerId,
        $refundToStoreCredit,
        $orderId,
        $creditmemo,
        $websiteId
    ) {
        $customerId = $this->storeCreditManagement->changeCustomerIdIfNeeded($customerId);

        return [$customerId, $refundToStoreCredit, $orderId, $creditmemo, $websiteId];
    }

    /**
     * Reimbursed spent store credit on order cancel
     *
     * @param \Aheadworks\StoreCredit\Model\Service\CustomerStoreCreditService $subject
     * @param int $customerId
     * @param int $refundToStoreCredit
     * @param OrderInterface $order
     * @param int $websiteId
     * @return array
     */
    public function beforeReimbursedSpentStoreCreditOrderCancel(
        $subject,
        $customerId,
        $refundToStoreCredit,
        $order,
        $websiteId
    ) {
        $customerId = $this->storeCreditManagement->changeCustomerIdIfNeeded($customerId);

        return [$customerId, $refundToStoreCredit, $order, $websiteId];
    }

    /**
     * Save transaction created by admin
     *
     * @param \Aheadworks\StoreCredit\Model\Service\CustomerStoreCreditService $subject
     * @param array $transactionData
     * @return array
     */
    public function beforeSaveAdminTransaction($subject, $transactionData)
    {
        $customerId = $transactionData['customer_id'];
        $rootCustomer = $this->storeCreditManagement->getRootUserByCustomerId($customerId);
        if ($rootCustomer) {
            $transactionData['customer_id'] = $rootCustomer->getId();
            $transactionData['customer_name'] = $rootCustomer->getFirstname() . ' ' . $rootCustomer->getLastname();
            $transactionData['customer_email'] = $rootCustomer->getEmail();
        }
        return [$transactionData];
    }

    /**
     * Retrieve customer store credit details
     *
     * @param \Aheadworks\StoreCredit\Model\Service\CustomerStoreCreditService $subject
     * @param \Closure $proceed
     * @param int $customerId
     * @return \Aheadworks\StoreCredit\Api\Data\CustomerStoreCreditDetailsInterface
     */
    public function aroundGetCustomerStoreCreditDetails($subject, $proceed, $customerId)
    {
        $rootCustomer = $this->storeCreditManagement->getRootUserByCustomerId($customerId);
        if ($rootCustomer) {
            $customerStcDetails = $proceed($rootCustomer->getId());
            $customerStcDetails = $this->storeCreditManagement
                ->changeCustomerStcDetailsIfNeeded($customerStcDetails);
        } else {
            $customerStcDetails = $proceed($customerId);
        }

        return $customerStcDetails;
    }
}
