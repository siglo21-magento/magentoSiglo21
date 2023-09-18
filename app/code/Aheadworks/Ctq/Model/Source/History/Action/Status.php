<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Source\History\Action;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Aheadworks\Ctq\Model\Source\History\Action
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Constants defined for history action status
     */
    const CREATED = 'created';
    const UPDATED = 'updated';
    const REMOVED = 'removed';
    const RECONFIGURED = 'reconfigured';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::CREATED, 'label' => __('Created')],
            ['value' => self::UPDATED, 'label' => __('Updated')],
            ['value' => self::REMOVED, 'label' => __('Removed')],
            ['value' => self::RECONFIGURED, 'label' => __('Reconfigured')]
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
