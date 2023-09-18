<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Source\Quote\Negotiation;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DiscountType
 *
 * @package Aheadworks\Ctq\Model\Source\Quote\Negotiation
 */
class DiscountType implements OptionSourceInterface
{
    /**#@+
     * Discount types
     */
    const PERCENTAGE_DISCOUNT = 'percent';
    const AMOUNT_DISCOUNT = 'amount';
    const PROPOSED_PRICE = 'proposed_price';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PERCENTAGE_DISCOUNT, 'label' => __('Percentage Discount')],
            ['value' => self::AMOUNT_DISCOUNT, 'label' => __('Amount Discount')],
            ['value' => self::PROPOSED_PRICE, 'label' => __('Proposed Subtotal')]
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        foreach ($this->toOptionArray() as $optionItem) {
            $options[$optionItem['value']] = ['label' => $optionItem['label']];
        }
        return $options;
    }
}
