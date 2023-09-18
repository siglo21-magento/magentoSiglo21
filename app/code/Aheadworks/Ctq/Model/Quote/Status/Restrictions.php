<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Status;

use Magento\Framework\DataObject;

/**
 * Class Restrictions
 * @package Aheadworks\Ctq\Model\Quote\Status
 */
class Restrictions extends DataObject implements RestrictionsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getNextAvailableStatuses()
    {
        return $this->getData(self::NEXT_AVAILABLE_STATUSES);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerAvailableActions()
    {
        return $this->getData(self::SELLER_AVAILABLE_ACTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function getBuyerAvailableActions()
    {
        return $this->getData(self::BUYER_AVAILABLE_ACTIONS);
    }
}
