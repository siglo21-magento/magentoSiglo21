<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Customer\Checker\EmailAvailability;

use Aheadworks\Ca\Api\Data\EmailAvailabilityResultInterface;

/**
 * Class Result
 *
 * @package Aheadworks\Ca\Model\Customer\Checker\EmailAvailability
 */
class Result implements EmailAvailabilityResultInterface
{
    /**
     * @var bool
     */
    private $isAvailableForCompany;

    /**
     * @var bool
     */
    private $isAvailableForCustomer;

    /**
     * @param bool $isAvailableForCompany
     * @param bool $isAvailableForCustomer
     */
    public function __construct(
        $isAvailableForCompany,
        $isAvailableForCustomer
    ) {
        $this->isAvailableForCompany = $isAvailableForCompany;
        $this->isAvailableForCustomer = $isAvailableForCustomer;
    }

    /**
     * @inheritdoc
     */
    public function isAvailableForCompany()
    {
        return $this->isAvailableForCompany;
    }

    /**
     * @inheritdoc
     */
    public function isAvailableForCustomer()
    {
        return $this->isAvailableForCustomer;
    }
}
