<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Observer\Payment;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Aheadworks\CreditLimit\Model\Checkout\ConfigProvider;

/**
 * Class OrderPaymentCancelObserver
 *
 * @package Aheadworks\CreditLimit\Observer\Payment
 */
class OrderPaymentCancelObserver implements ObserverInterface
{
    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $method = $observer->getPayment()->getMethodInstance();
        if ($method->getCode() == ConfigProvider::METHOD_CODE) {
            $method->cancel($observer->getPayment());
        }
    }
}
