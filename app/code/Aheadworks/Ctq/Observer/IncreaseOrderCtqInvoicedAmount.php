<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Class IncreaseOrderCtqInvoicedAmount
 *
 * @package Aheadworks\Ctq\Observer
 */
class IncreaseOrderCtqInvoicedAmount implements ObserverInterface
{
    /**
     * Increase order aw_ctq_invoiced attribute based on created invoice
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        if ($invoice->getBaseAwCtqAmount()) {
            $order->setBaseAwCtqInvoiced(
                $order->getBaseAwCtqInvoiced() + $invoice->getBaseAwCtqAmount()
            );
            $order->setAwCtqInvoiced(
                $order->getAwCtqInvoiced() + $invoice->getAwCtqAmount()
            );
        }
        return $this;
    }
}
