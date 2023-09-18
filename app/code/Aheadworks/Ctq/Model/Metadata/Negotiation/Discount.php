<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Metadata\Negotiation;

use Magento\Framework\DataObject;

/**
 * Class Discount
 *
 * @package Aheadworks\Ctq\Model\Metadata\Negotiation
 */
class Discount extends DataObject implements NegotiatedDiscountInterface
{
    /**
     * @inheritdoc
     */
    public function getDiscountType()
    {
        return $this->getData(self::DISCOUNT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountType($discountType)
    {
        return $this->setData(self::DISCOUNT_TYPE, $discountType);
    }

    /**
     * @inheritdoc
     */
    public function getDiscountValue()
    {
        return $this->getData(self::DISCOUNT_VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountValue($discountValue)
    {
        return $this->setData(self::DISCOUNT_VALUE, $discountValue);
    }
}
