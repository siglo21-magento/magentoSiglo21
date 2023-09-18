<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Comment\Processor;

use Aheadworks\CreditLimit\Model\Source\Transaction\EntityType;
use Aheadworks\CreditLimit\Model\Sales\OrderFinder;
use Magento\Framework\UrlInterface;
use Magento\Framework\Phrase\Renderer\Placeholder;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class OrderProcessor
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Comment\Processor
 */
class OrderProcessor implements ProcessorInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Placeholder
     */
    private $placeholder;

    /**
     * @var OrderFinder
     */
    private $orderFinder;

    /**
     * @param UrlInterface $urlBuilder
     * @param Placeholder $placeholder
     * @param OrderFinder $orderFinder
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Placeholder $placeholder,
        OrderFinder $orderFinder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->placeholder = $placeholder;
        $this->orderFinder = $orderFinder;
    }

    /**
     * @inheritdoc
     */
    public function renderComment($entities, $isUrl)
    {
        $arguments = [];
        foreach ($entities as $entity) {
            if ($entity->getEntityType() != EntityType::ORDER_ID) {
                continue;
            }

            $orderIncrementId = '#' . $entity->getEntityLabel();
            if ($isUrl) {
                $orderId = $this->findOrderId($entity->getEntityLabel());
                if ($orderId) {
                    $url = $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $orderId]);
                    $orderIncrementId = $this->placeholder->render(
                        ['<a href="%order_url">%order_id</a>'],
                        ['order_id' => $orderIncrementId, 'order_url' => $url]
                    );
                }
            }
            $arguments['order_id'] = $orderIncrementId;
        }

        return $arguments;
    }

    /**
     * Find order ID by increment ID
     *
     * @param int $incrementId
     * @return int|null
     */
    private function findOrderId($incrementId)
    {
        $order = $this->orderFinder->findOrderByIncrementId($incrementId);
        if ($order instanceof OrderInterface) {
            return $order->getEntityId();
        }

        return null;
    }
}
