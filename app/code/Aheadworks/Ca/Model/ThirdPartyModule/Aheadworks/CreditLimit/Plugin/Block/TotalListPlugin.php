<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin\Block;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\CreditLimitManagement;

/**
 * Class TotalListPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin\Block
 */
class TotalListPlugin
{
    /**
     * @var CreditLimitManagement
     */
    private $creditLimitManagement;

    /**
     * @param CreditLimitManagement $creditLimitManagement
     */
    public function __construct(
        CreditLimitManagement $creditLimitManagement
    ) {
        $this->creditLimitManagement = $creditLimitManagement;
    }

    /**
     * Change customer to company admin if required
     *
     * @param \Aheadworks\CreditLimit\Block\Customer\TotalList $subject
     * @param int $customerId
     * @return int
     */
    public function afterGetCustomerId($subject, $customerId)
    {
        $customerId = $this->creditLimitManagement->changeCustomerIdIfNeeded($customerId);
        return $customerId;
    }
}
