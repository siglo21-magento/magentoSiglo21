<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Admin\Session;

use Magento\Backend\Model\Session\Quote as QuoteSession;
use Magento\Quote\Model\Quote as QuoteModel;

/**
 * Class Quote
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Session
 */
class Quote extends QuoteSession
{
    /**
     * Set quote object to session
     *
     * @param QuoteModel $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->_quote = $quote;
        return $this;
    }
}
