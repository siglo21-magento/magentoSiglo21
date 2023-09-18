<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Quote;

/**
 * Class Buy
 * @package Aheadworks\Ctq\Controller\Quote
 */
class Buy extends ChangeStatus
{
    /**
     * {@inheritdoc}
     */
    protected function performSave($status)
    {
        $quoteId = $this->getQuote()->getId();
        $storeId = $this->storeManager->getStore()->getId();
        $this->buyerQuoteManagement->buy($quoteId, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    protected function redirectTo($resultRedirect)
    {
        return $resultRedirect->setPath('checkout');
    }
}
