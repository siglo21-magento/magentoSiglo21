<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\QuoteList;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Tax\Helper\Data as TaxData;

/**
 * Class Grid
 * @package Aheadworks\Ctq\ViewModel\QuoteList
 */
class Grid implements ArgumentInterface
{
    /**
     * @var TaxData
     */
    private $taxHelper;

    /**
     * @param TaxData $taxHelper
     */
    public function __construct(
        TaxData $taxHelper
    ) {
        $this->taxHelper = $taxHelper;
    }

    /**
     * Get is display both prices
     *
     * @return bool
     */
    public function getIsDisplayBothPrices()
    {
        return $this->taxHelper->displayCartBothPrices();
    }
}
