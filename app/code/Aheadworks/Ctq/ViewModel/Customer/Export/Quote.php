<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\Customer\Export;

use Aheadworks\Ctq\ViewModel\Customer\Quote as CustomerQuote;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Quote
 * @package Aheadworks\Ctq\ViewModel\Customer\Export
 */
class Quote extends CustomerQuote implements ArgumentInterface
{
    /**
     * @inheritDoc
     */
    public function isAllowSorting($quote)
    {
        return false;
    }
}
