<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\QuoteList;

use Aheadworks\Ctq\Model\Request\Checker;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * Class DiscountProcessor
 * @package Aheadworks\Ctq\Block\QuoteList
 */
class DiscountProcessor implements LayoutProcessorInterface
{
    /**
     * @var Checker
     */
    private $checker;

    /**
     * @param Checker $checker
     */
    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    /**
     * @inheritDoc
     */
    public function process($jsLayout)
    {
        unset($jsLayout['components']['block-totals']['children']['discount']);

        return $jsLayout;
    }
}
