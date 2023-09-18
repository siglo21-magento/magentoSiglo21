<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Owner
 * @package Aheadworks\Ctq\Model\Source\Comment
 */
class Owner implements OptionSourceInterface
{
    /**#@+
     * Constants defined for RMA status types
     */
    const SELLER = 'seller';
    const BUYER = 'buyer';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SELLER, 'label' => __('Seller')],
            ['value' => self::BUYER, 'label' => __('Buyer')]
        ];
    }
}
