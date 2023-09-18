<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Admin\Quote;

use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;

/**
 * Class Reloader
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Quote
 */
class Reloader
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
     * Reload a quote stored in session
     */
    public function reload()
    {
        $quote = $this->quoteSession->getQuote();
        if ($quote) {
            $quote->load($quote->getId());
        }
    }
}
