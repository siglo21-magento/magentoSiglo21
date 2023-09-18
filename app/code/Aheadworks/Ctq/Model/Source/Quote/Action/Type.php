<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Source\Quote\Action;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Aheadworks\Ctq\Model\Source\Quote\Action
 */
class Type implements OptionSourceInterface
{
    /**#@+
     * Constants defined for quote action types
     */
    const EDIT = 'edit';
    const EDIT_ITEMS_ORDER = 'edit_items_order';
    const EXPORT = 'export';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::EDIT, 'label' => __('Edit')],
            ['value' => self::EDIT_ITEMS_ORDER, 'label' => __('Edit Items Order')],
            ['value' => self::EXPORT, 'label' => __('Export')]
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
            $options[$optionItem['value']] = $optionItem['label'];
        }
        return $options;
    }
}
