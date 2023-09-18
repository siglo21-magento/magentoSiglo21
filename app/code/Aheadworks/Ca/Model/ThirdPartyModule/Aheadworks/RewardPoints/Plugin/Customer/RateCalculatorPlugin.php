<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer;

use Aheadworks\RewardPoints\Api\Data\SpendRateInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;

/**
 * Class RateCalculatorPlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer
 */
class RateCalculatorPlugin
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
     * Calculate reward discount
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\RateCalculator $subject
     * @param int $customerId
     * @param int $points
     * @param int|null $websiteId
     * @param SpendRateInterface $rate
     * @return array
     */
    public function beforeCalculateRewardDiscount($subject, $customerId, $points, $websiteId = null, $rate = null)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);

        return [$customerId, $points, $websiteId, $rate];
    }
}
