<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Source\Job;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 *
 * @package Aheadworks\CreditLimit\Model\Source\Job
 */
class Type implements OptionSourceInterface
{
    /**
     * Job type for changing credit limit value
     */
    const UPDATE_CREDIT_LIMIT = 'update_credit_limit';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::UPDATE_CREDIT_LIMIT,
                'label' => __('Update Credit Limit')
            ]
        ];
    }
}
