<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\ViewModel\Order\Column;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class CreatedBy
 * @package Aheadworks\Ca\ViewModel\Order\Column
 */
class CreatedBy implements ArgumentInterface
{
    /**
     * Retrieve customer name from order
     *
     * @param OrderInterface $order
     * @return string
     */
    public function getCreatedBy($order)
    {
        return $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
    }
}
