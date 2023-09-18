<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Plugin\Model\Quote\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;

/**
 * Class AwRewardPointsPlugin
 * @package Aheadworks\Ctq\Plugin\Model\Quote\Total
 */
class AwRewardPointsPlugin extends AbstractResetTotalPlugin
{
    /**
     * @inheritdoc
     */
    protected function updateBeforeReset(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        $quote->setAwUseRewardPoints(false);
        foreach ($shippingAssignment->getItems() as $item) {
            $item->setAwRewardPointsAmount(0);
            $item->setBaseAwRewardPointsAmount(0);
            $item->setAwRewardPoints(0);

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $child->setAwRewardPointsAmount(0);
                    $child->setBaseAwRewardPointsAmount(0);
                    $child->setAwRewardPoints(0);
                }
            }
        }
        return true;
    }
}
