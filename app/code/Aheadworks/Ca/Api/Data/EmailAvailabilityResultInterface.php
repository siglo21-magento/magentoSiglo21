<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api\Data;

/**
 * Interface EmailAvailabilityResultInterface
 *
 * @package Aheadworks\Ca\Api\Data
 */
interface EmailAvailabilityResultInterface
{
    /**
     * Is email available for company
     *
     * @return bool
     */
    public function isAvailableForCompany();

    /**
     * Is email available for customer
     *
     * @return bool
     */
    public function isAvailableForCustomer();
}
