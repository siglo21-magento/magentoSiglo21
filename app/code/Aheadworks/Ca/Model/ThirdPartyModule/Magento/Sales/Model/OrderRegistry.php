<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Magento\Sales\Model;

use Magento\Sales\Model\Order;

/**
 * Class OrderRegistry
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Magento\Sales\Model
 */
class OrderRegistry
{
    /**
     * @var Order
     */
    private $order;

    /**
     * Retrieve order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set order
     *
     * @param Order $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }
}
