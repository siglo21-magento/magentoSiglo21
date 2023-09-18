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
 * Class EnterpriseRewardPointsPlugin
 * @package Aheadworks\Ctq\Plugin\Model\Quote\Total
 */
class EnterpriseRewardPointsPlugin extends AbstractResetTotalPlugin
{
    /**
     * @inheritdoc
     */
    protected function updateBeforeReset(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        if ($quote->getUseRewardPoints()) {
            $this->discountUsed = true;
            $quote->setUseRewardPoints(false);
        }
        return true;
    }
}
