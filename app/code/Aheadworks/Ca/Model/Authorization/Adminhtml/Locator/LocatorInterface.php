<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Authorization\Adminhtml\Locator;

/**
 * Interface LocatorInterface
 *
 * @package Aheadworks\Ca\Model\Authorization\Adminhtml\Locator
 */
interface LocatorInterface
{
    /**
     * Get customer id depending on the page in backend you are on
     *
     * @return int|null
     */
    public function getCustomerId();
}
