<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

/**
 * Class CtqDiscount
 *
 * @package Aheadworks\Ctq\Model\Total\Creditmemo
 */
class CtqDiscount extends AbstractTotal
{
    /**
     * @inheritdoc
     */
    public function collect(Creditmemo $creditmemo)
    {
        $creditmemo->setAwCtqAmount(0);
        $creditmemo->setBaseAwCtqAmount(0);

        $order = $creditmemo->getOrder();
        if ($order->getBaseAwCtqAmount() && $order->getBaseAwCtqInvoiced() != 0) {
            $totalAmount = 0;
            $baseTotalAmount = 0;

            /** @var $item \Magento\Sales\Model\Order\Creditmemo\Item */
            foreach ($creditmemo->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }

                $orderItemAmount = (double)$orderItem->getAwCtqInvoiced();
                $baseOrderItemAmount = (double)$orderItem->getBaseAwCtqInvoiced();
                $orderItemQty = $orderItem->getQtyInvoiced();

                if ($orderItemAmount && $orderItemQty) {
                    // Resolve rounding problems
                    $amount = $orderItemAmount - $orderItem->getAwCtqRefunded();
                    $baseAmount = $baseOrderItemAmount - $orderItem->getBaseAwCtqRefunded();
                    if (!$item->isLast()) {
                        $activeQty = $orderItemQty - $orderItem->getQtyRefunded();
                        $amount = $creditmemo->roundPrice(
                            $amount / $activeQty * $item->getQty(),
                            'regular',
                            true
                        );
                        $baseAmount = $creditmemo->roundPrice(
                            $baseAmount / $activeQty * $item->getQty(),
                            'base',
                            true
                        );
                    }

                    $item->setAwCtqAmount($amount);
                    $item->setBaseAwCtqAmount($baseAmount);

                    $totalAmount += $amount;
                    $baseTotalAmount += $baseAmount;
                }
            }

            if ($baseTotalAmount > 0) {
                $creditmemo->setBaseAwCtqAmount(-$baseTotalAmount);
                $creditmemo->setAwCtqAmount(-$totalAmount);

                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalAmount);
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalAmount);
            }
        }

        if ($creditmemo->getGrandTotal() <= 0) {
            $creditmemo->setAllowZeroGrandTotal(true);
        }

        return $this;
    }
}
