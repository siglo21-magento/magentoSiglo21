<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\QuoteList;

use Magento\Quote\Model\Quote;

/**
 * Class Checker
 * @package Aheadworks\Ctq\Model\QuoteList
 */
class Checker
{
    /**
     * Check is CTQ Quote
     *
     * @param Quote $quote
     * @return bool
     */
    public function isAwCtqQuote($quote)
    {
        return (bool)$quote->getAwCtqQuoteListCustomerId();
    }
}
