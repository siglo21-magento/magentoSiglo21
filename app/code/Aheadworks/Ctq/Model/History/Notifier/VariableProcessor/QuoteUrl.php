<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\History\Notifier\VariableProcessor;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Email\VariableProcessorInterface;
use Aheadworks\Ctq\Model\Quote\Url;
use Aheadworks\Ctq\Model\Source\History\EmailVariables;

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
        $quoteId = $quote->getId();
        $variables[EmailVariables::QUOTE_URL] = $variables[EmailVariables::IS_SELLER]
            ? $this->url->getAdminQuoteUrl($quoteId)
            : $this->url->getFrontendQuoteUrl($quoteId);

        return $variables;
    }
}
