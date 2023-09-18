<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Authorization\Adminhtml\Locator;

use Magento\Backend\Model\Session\Quote as QuoteSession;

/**
 * Class Locator
 *
 * @package Aheadworks\Ca\Model\Authorization\Adminhtml\Locator
 */
class CustomerLocator implements LocatorInterface
{
    /**
     * @var QuoteSession
     */
    private $quoteSession;

    /**
     * @param QuoteSession $quoteSession
     */
    public function __construct(
        QuoteSession $quoteSession
    ) {
        $this->quoteSession = $quoteSession;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        if ($customerId = $this->quoteSession->getCustomerId()) {
            return $customerId;
        }

        return null;
    }
}
