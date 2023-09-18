<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Expiration\Notifier\VariableProcessor;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Email\VariableProcessorInterface;
use Aheadworks\Ctq\Model\Quote\Url;
use Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder\EmailVariables;

/**
 * Class QuoteUrl
 *
 * @package Aheadworks\Raf\Model\Advocate\Email\Processor\VariableProcessor
 */
class QuoteUrl implements VariableProcessorInterface
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
        /** @var QuoteInterface $quote */
        $quote = $variables[EmailVariables::QUOTE];
        $variables[EmailVariables::QUOTE_URL] = $this->url->getFrontendQuoteUrl($quote->getId());

        return $variables;
    }
}
