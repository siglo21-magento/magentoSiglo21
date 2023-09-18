<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\QuoteList;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ctq\ViewModel\Checkout\RequestQuoteLink as CheckoutRequestQuoteLink;

/**
 * Class RequestQuoteLink
 * @package Aheadworks\Ctq\ViewModel\QuoteList
 */
class RequestQuoteLink extends CheckoutRequestQuoteLink implements ArgumentInterface
{
    /**
     * {@inheritDoc}
     */
    public function isRequestQuoteButtonAvailable()
    {
        $cartId = $this->checkoutSession->getAwCtqQuoteListId();
        return $this->buyerPermissionManagement->canRequestQuoteList($cartId);
    }
}
