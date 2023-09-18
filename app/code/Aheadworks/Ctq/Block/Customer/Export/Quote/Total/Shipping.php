<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Customer\Export\Quote\Total;

use Magento\Tax\Block\Checkout\Shipping as TaxShipping;

/**
 * Class Shipping
 * @package Aheadworks\Ctq\Block\Customer\Export\Quote\Total
 */
class Shipping extends TaxShipping
{
    /**
     * @inheritDoc
     */
    public function displayShipping()
    {
        return $this->getTotal()->getValue();
    }
}
