<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Plugin\Model\Authentication;

use Aheadworks\Ca\Model\ThirdPartyModule\Magento\Sales\Model\OrderRegistry;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorization;
use Magento\Sales\Model\Order;

/**
 * Class OrderViewAuthorizationPlugin
 * @package Aheadworks\Ca\Plugin\Model\Authentication
 */
class OrderViewAuthorizationPlugin
{
    /**
     * @var OrderRegistry
     */
    private $orderRegistry;

    /**
     * @param OrderRegistry $orderRegistry
     */
    public function __construct(OrderRegistry $orderRegistry)
    {
        $this->orderRegistry = $orderRegistry;
    }

    /**
     * Set order to registry for check authentication
     *
     * @param OrderViewAuthorization $subject
     * @param Order $order
     * @return void
     */
    public function beforeCanView($subject, Order $order)
    {
        $this->orderRegistry->setOrder($order);
    }
}
