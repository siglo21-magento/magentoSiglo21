<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Company\Notifier\VariableProcessor;

use Aheadworks\Ca\Model\Email\VariableProcessorInterface;
use Aheadworks\Ca\Model\Source\Company\EmailVariables;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class UserName
 * @package Aheadworks\Ca\Model\Company\Notifier\VariableProcessor
 */
class UserName implements VariableProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        /** @var CustomerInterface $customer */
        $customer = $variables[EmailVariables::CUSTOMER];
        $variables[EmailVariables::USER_NAME] = $customer->getFirstname() . ' ' . $customer->getLastname();

        return $variables;
    }
}
