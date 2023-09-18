<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Customer\Notifier\VariableProcessor;

use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\CreditLimit\Model\Email\VariableProcessorInterface;
use Aheadworks\CreditLimit\Model\Source\Customer\EmailVariables;

/**
 * Class CustomerName
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Notifier\VariableProcessor
 */
class CustomerName implements VariableProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        /** @var array $customer */
        $customer = $variables[EmailVariables::CUSTOMER];
        $variables[EmailVariables::CUSTOMER_NAME] = $this->prepareCustomerName($customer);

        return $variables;
    }

    /**
     * Prepare customer name
     *
     * @param array $customer
     * @return string
     */
    private function prepareCustomerName($customer)
    {
        return $customer[CustomerInterface::FIRSTNAME] . ' ' . $customer[CustomerInterface::LASTNAME];
    }
}
