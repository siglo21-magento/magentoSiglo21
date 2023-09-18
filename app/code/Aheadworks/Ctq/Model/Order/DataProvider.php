<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class DataProvider
 * @package Aheadworks\Ctq\Model\Order
 */
class DataProvider
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Retrieve order
     *
     * @param int $orderId
     * @return OrderInterface|null
     */
    public function getOrder($orderId)
    {
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (LocalizedException $e) {
            $order = null;
        }

        return $order;
    }

    /**
     * Retrieve order increment id
     *
     * @param int $orderId
     * @return string|null
     */
    public function getOrderIncrementId($orderId)
    {
        $order = $this->getOrder($orderId);

        return $order ? $order->getIncrementId() : '';
    }
}
