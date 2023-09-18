<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

/**
 * Class Sell
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class Sell extends Save
{
    /**
     * {@inheritdoc}
     */
    protected function redirectTo($resultRedirect, $quote)
    {
        $this->sellerQuoteManagement->sell($quote);
        return $resultRedirect->setPath('sales/order_create');
    }
}
