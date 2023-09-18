<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;

/**
 * Class PointsSummaryServicePlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer
 */
class PointsSummaryServicePlugin
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
     * Check reward points limit usage
     *
     * @param \Aheadworks\RewardPoints\Model\Service\PointsSummaryService $subject
     * @param callable $proceed
     * @param int $customerId
     * @return int
     */
    public function aroundGetCustomerRewardPointsBalance($subject, callable $proceed, $customerId)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        $balance = $proceed($customerId);
        $balance = $this->rewardPointsManagement->applyRewardPointsLimitIfNeeded($balance);

        return $balance;
    }
}
