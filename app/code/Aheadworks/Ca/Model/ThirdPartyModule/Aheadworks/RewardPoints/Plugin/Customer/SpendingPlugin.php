<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote\Address;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;
use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * Class SpendingPlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer
 */
class SpendingPlugin
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
     * Quote item reward points calculation process
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\Spending $subject
     * @param AbstractItem $item
     * @param int $customerId
     * @param int $websiteId
     * @return array
     */
    public function beforeProcess($subject, AbstractItem $item, $customerId, $websiteId)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$item, $customerId, $websiteId];
    }

    /**
     * Distribute reward points at parent item to children items
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\Spending $subject
     * @param AbstractItem $item
     * @param int $customerId
     * @param int $websiteId
     * @return array
     */
    public function beforeDistributeRewardPoints($subject, AbstractItem $item, $customerId, $websiteId)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$customerId, $websiteId];
    }

    /**
     * Shipping reward points calculation process
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\Spending $subject
     * @param AddressInterface $address
     * @param int $customerId
     * @param int $websiteId
     * @return array
     */
    public function beforeProcessShipping($subject, AddressInterface $address, $customerId, $websiteId)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$address, $customerId, $websiteId];
    }

    /**
     * Retrieve calculate Reward Points amount for applying
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\Spending $subject
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $items
     * @param AddressInterface|Address $address
     * @param int $customerId
     * @param int $websiteId
     * @return array
     */
    public function beforeCalculateAmountForRewardPoints(
        $subject,
        $items,
        AddressInterface $address,
        $customerId,
        $websiteId
    ) {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        return [$items, $address, $customerId, $websiteId];
    }
}
