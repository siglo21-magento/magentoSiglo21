<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\Data\CartInterface as QuoteCartInterface;
use Aheadworks\Ctq\Api\Data\CartInterface;

/**
 * Class UnsetCustomerId
 * @package Aheadworks\Ctq\Observer
 */
class UnsetCustomerId implements ObserverInterface
{
    /**
     * Unset customer id for quote list
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        $quote = $event->getDataObject();

        if ($quote instanceof QuoteCartInterface
            && $quote->getData(CartInterface::AW_CTQ_QUOTE_LIST_CUSTOMER_ID)
            && $quote->getIsActive()
        ) {
            $quote->unsCustomerId();
        }
    }
}
