<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 *
 * @package Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Reminder email status list
     */
    const READY_TO_BE_SENT = 'ready_to_be_sent';
    const SENT = 'sent';
    const FAILED = 'failed';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::READY_TO_BE_SENT,
                'label' => __('Ready to be Sent')
            ],
            [
                'value' => self::SENT,
                'label' => __('Sent')
            ],
            [
                'value' => self::FAILED,
                'label' => __('Failed')
            ]
        ];
    }
}
