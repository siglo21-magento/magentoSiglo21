<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Observer\Payment;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;

/**
 * Class PaymentMethodAssignDataObserver
 *
 * @package Aheadworks\CreditLimit\Observer\Payment
 */
class PaymentMethodAssignDataObserver implements ObserverInterface
{
    /**
     * Assign payment method data
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $poNumber = $observer->getData(AbstractDataAssignObserver::DATA_CODE)->getPoNumber();
        if ($poNumber) {
            $observer->getPaymentModel()->setPoNumber($poNumber);
        }
    }
}
