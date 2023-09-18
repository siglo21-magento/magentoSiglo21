<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\Totals;

use Magento\Sales\Block\Adminhtml\Order\Create\Totals\DefaultTotals as SalesDefaultTotals;

/**
 * Class DefaultTotals
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\Totals
 */
class DefaultTotals extends SalesDefaultTotals
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'Aheadworks_Ctq::quote/edit/totals/default.phtml';
}
