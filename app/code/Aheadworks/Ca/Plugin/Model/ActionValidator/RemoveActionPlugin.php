<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Plugin\Model\ActionValidator;

use Magento\Customer\Model\Customer;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ActionValidator\RemoveAction;

/**
 * Class RemoveActionPlugin
 * @package Aheadworks\Ca\Plugin\Model\ActionValidator
 */
class RemoveActionPlugin
{
    /**
     * Modify area permission for delete customer
     *
     * @param RemoveAction $subject
     * @param $result
     * @param AbstractModel $model
     * @return bool
     */
    public function afterIsAllowed(
        RemoveAction $subject,
        $result,
        AbstractModel $model
    ) {
        $return = $result;

        if (!$return && $model instanceof Customer) {
            $return = true;
        }

        return $return;
    }
}
