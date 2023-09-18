<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;

/**
 * Class EarningPlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer
 */
class EarningPlugin
{
    /**
     * @var RewardPointsManagement
     */
    private $rewardPointsManagement;

    /**
     * @param RewardPointsManagement $rewardPointsManagement
     */
    public function __construct(
        RewardPointsManagement $rewardPointsManagement
    ) {
        $this->rewardPointsManagement = $rewardPointsManagement;
    }

    /**
     * Retrieve calculation earning points value by quote
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\Earning $subject
     * @param CartInterface|Quote $quote
     * @param int|null $customerId
     * @param int|null $websiteId
     * @return array
     */
    public function beforeCalculationByQuote($subject, $quote, $customerId, $websiteId = null)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$quote, $customerId, $websiteId];
    }

    /**
     * Retrieve calculation earning points value by invoice
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\Earning $subject
     * @param InvoiceInterface $invoice
     * @param int $customerId
     * @param int|null $websiteId
     * @return array
     */
    public function beforeCalculationByInvoice($subject, $invoice, $customerId, $websiteId = null)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$invoice, $customerId, $websiteId];
    }

    /**
     * Retrieve calculation earning points value by credit memo
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\Earning $subject
     * @param CreditmemoInterface $creditmemo
     * @param int $customerId
     * @param int|null $websiteId
     * @return array
     */
    public function beforeCalculationByCreditmemo($subject, $creditmemo, $customerId, $websiteId = null)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$creditmemo, $customerId, $websiteId];
    }

    /**
     * Retrieve calculation earning points value by product.
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\Earning $subject
     * @param ProductInterface $product
     * @param bool $mergeRuleIds
     * @param int|null $customerId
     * @param int|null $websiteId
     * @param int|null $customerGroupId
     * @return array
     */
    public function beforeCalculationByProduct(
        $subject,
        $product,
        $mergeRuleIds,
        $customerId,
        $websiteId = null,
        $customerGroupId = null
    ) {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$product, $mergeRuleIds, $customerId, $websiteId, $customerGroupId];
    }
}
