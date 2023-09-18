<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Customer\Notifier\VariableProcessor;

use Aheadworks\CreditLimit\Model\Email\VariableProcessorInterface;
use Aheadworks\CreditLimit\Model\Source\Customer\EmailVariables;
use Magento\Framework\Url as FrontendUrl;

/**
 * Class BalanceInfoUrl
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Notifier\VariableProcessor
 */
class BalanceInfoUrl implements VariableProcessorInterface
{
    /**
     * @var FrontendUrl
     */
    private $url;

    /**
     * @param FrontendUrl $url
     */
    public function __construct(FrontendUrl $url)
    {
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        $variables[EmailVariables::BALANCE_INFO_URL] = $this->url->getUrl('aw_credit_limit/balance');
        return $variables;
    }
}
