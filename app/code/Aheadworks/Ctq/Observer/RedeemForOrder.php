<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order as SalesOrder;
use Magento\Quote\Model\Quote as Quote;

/**
 * Class RedeemForOrder
 *
 * @package Aheadworks\Ctq\Observer
 */
class RedeemForOrder implements ObserverInterface
{
    /**
     * Convert quote data to order data
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var $order SalesOrder **/
        $order = $event->getOrder();
        /** @var $quote Quote */
        $quote = $event->getQuote();

        if ($quote->getAwCtqAmount()) {
            $order->setAwCtqAmount($quote->getAwCtqAmount());
            $order->setBaseAwCtqAmount($quote->getBaseAwCtqAmount());
        }
    }
}
