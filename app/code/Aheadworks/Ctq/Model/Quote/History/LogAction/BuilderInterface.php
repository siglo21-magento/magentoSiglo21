<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\History\LogAction;

use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote;

/**
 * Interface BuilderInterface
 * @package Aheadworks\Ctq\Model\Quote\History\LogAction
 */
interface BuilderInterface
{
    /**
     * Build history action from quote object
     *
     * @param QuoteInterface|Quote $quote
     * @return HistoryActionInterface[]
     */
    public function build($quote);
}
