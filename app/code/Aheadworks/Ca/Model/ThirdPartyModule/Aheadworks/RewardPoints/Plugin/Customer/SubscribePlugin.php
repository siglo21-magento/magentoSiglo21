<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;

/**
 * Class SubscribePlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer
 */
class SubscribePlugin
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
     * Show block or not
     *
     * @param \Aheadworks\RewardPoints\Block\Customer\Subscribe $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundCanShow($subject, $proceed)
    {
        $result = false;
        if ($this->rewardPointsManagement->isAvailableSubscribeOptions()) {
            $result = $proceed();
        }

        return $result;
    }
}
