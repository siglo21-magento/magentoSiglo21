<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Source\Job;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 *
 * @package Aheadworks\CreditLimit\Model\Source\Job
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Async job status values
     */
    const READY = 'ready';
    const DONE = 'done';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::READY,
                'label' => __('Ready')
            ],
            [
                'value' => self::DONE,
                'label' => __('Done')
            ]
        ];
    }
}
