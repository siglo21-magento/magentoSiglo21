<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Metadata\Negotiation;

/**
 * Interface NegotiatedDiscountInterface
 *
 * @package Aheadworks\Ctq\Model\Metadata\Negotiation
 */
interface NegotiatedDiscountInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const DISCOUNT_TYPE = 'discount_type';
    const DISCOUNT_VALUE = 'discount_value';
    /**#@-*/

    /**
     * Get discount type
     *
     * @return string
     */
    public function getDiscountType();

    /**
     * Set discount type
     *
     * @param int $discountType
     * @return $this
     */
    public function setDiscountType($discountType);

    /**
     * Get discount value
     *
     * @return string|float
     */
    public function getDiscountValue();

    /**
     * Set discount value
     *
     * @param string|float $discountValue
     * @return $this
     */
    public function setDiscountValue($discountValue);
}
