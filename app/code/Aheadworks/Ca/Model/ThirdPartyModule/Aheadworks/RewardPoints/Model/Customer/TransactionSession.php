<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;

/**
 * Class TransactionSession
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\Customer
 */
class TransactionSession extends CustomerSession
{
    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        $customerId = parent::getCustomerId();
        $customerId = $this->getRewardPointsManagement()->changeCustomerIdIfNeededForTransaction($customerId);

        return $customerId;
    }

    /**
     * Retrieve reward points management
     *
     * @return RewardPointsManagement
     */
    private function getRewardPointsManagement()
    {
        return ObjectManager::getInstance()->get(RewardPointsManagement::class);
    }
}
