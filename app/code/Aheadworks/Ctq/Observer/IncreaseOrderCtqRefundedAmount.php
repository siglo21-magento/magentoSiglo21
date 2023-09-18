<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Class IncreaseOrderCtqRefundedAmount
 *
 * @package Aheadworks\Ctq\Observer
 */
class IncreaseOrderCtqRefundedAmount implements ObserverInterface
{
    /**
     * Increase order aw_ctq_refunded attribute based on created creditmemo
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        if ($creditmemo->getBaseAwCtqAmount()) {
            $order->setBaseAwCtqRefunded($order->getBaseAwCtqRefunded() + $creditmemo->getBaseAwCtqAmount());
            $order->setAwCtqRefunded($order->getAwCtqRefunded() + $creditmemo->getAwCtqAmount());
        }

        return $this;
    }
}
