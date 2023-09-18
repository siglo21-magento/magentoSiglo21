<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Authorization\Adminhtml;

use Magento\Authorization\Model\UserContextInterface;
use Aheadworks\Ca\Model\Authorization\Adminhtml\Locator\CustomerLocator;

/**
 * Class CompanyUserContext
 *
 * @package Aheadworks\Ca\Model\Authorization\Adminhtml
 */
class CompanyUserContext implements UserContextInterface
{
    /**
     * @var CustomerLocator
     */
    private $locator;

    /**
     * @param CustomerLocator $locator
     */
    public function __construct(
        CustomerLocator $locator
    ) {
        $this->locator = $locator;
    }

    /**
     * @inheritdoc
     */
    public function getUserId()
    {
        return $this->locator->getCustomerId();
    }

    /**
     * @inheritdoc
     */
    public function getUserType()
    {
        return UserContextInterface::USER_TYPE_CUSTOMER;
    }
}
