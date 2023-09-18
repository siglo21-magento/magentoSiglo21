<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote;

/**
 * Interface ValidatorInterface
 * @package Aheadworks\Ctq\Model\Quote
 */
interface ValidatorInterface
{
    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * @param Quote|QuoteInterface $quote
     * @return bool
     */
    public function isValid($quote);
}
