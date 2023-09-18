<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\StoreCreditManagement;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;

/**
 * Class TransactionSession
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\Customer
 */
class TransactionSession extends CustomerSession
{
    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        $customerId = parent::getCustomerId();
        $customerId = $this->getStoreCreditManagement()->changeCustomerIdIfNeededForTransaction($customerId);

        return $customerId;
    }

    /**
     * Retrieve store credit management
     *
     * @return StoreCreditManagement
     */
    private function getStoreCreditManagement()
    {
        return ObjectManager::getInstance()->get(StoreCreditManagement::class);
    }
}
