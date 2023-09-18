<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Company\Notifier\VariableProcessor;

use Aheadworks\Ca\Model\Email\VariableProcessorInterface;
use Aheadworks\Ca\Model\Source\Company\EmailVariables;
use Aheadworks\Ca\Model\Url;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class ResetPasswordUrl
 * @package Aheadworks\Ca\Model\Company\Notifier\VariableProcessor
 */
class ResetPasswordUrl implements VariableProcessorInterface
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @param Url $url
     */
    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        /** @var CustomerInterface $customer */
        $customer = $variables[EmailVariables::CUSTOMER];
        $variables[EmailVariables::RESET_PASSWORD_URL] = $this->url->getResetPasswordUrl($customer);

        return $variables;
    }
}
