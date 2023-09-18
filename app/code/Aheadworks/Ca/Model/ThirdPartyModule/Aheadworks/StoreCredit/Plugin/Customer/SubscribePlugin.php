<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\StoreCreditManagement;

/**
 * Class SubscribePlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Customer
 */
class SubscribePlugin
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
     * Show block or not
     *
     * @param \Aheadworks\StoreCredit\Block\Customer\Subscribe $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundCanShow($subject, $proceed)
    {
        $result = false;
        if ($this->storeCreditManagement->isAvailableSubscribeOptions()) {
            $result = $proceed();
        }

        return $result;
    }
}
