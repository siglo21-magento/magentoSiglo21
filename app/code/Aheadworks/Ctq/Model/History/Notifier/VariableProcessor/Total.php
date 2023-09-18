<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\History\Notifier\VariableProcessor;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Email\VariableProcessorInterface;
use Aheadworks\Ctq\Model\Source\History\EmailVariables;
use Aheadworks\Ctq\ViewModel\Customer\Quote;

/**
 * Class Total
 * @package Aheadworks\Ctq\Model\History\Notifier\VariableProcessor
 */
class Total implements VariableProcessorInterface
{
    /**
     * @var Quote
     */
    private $quoteViewModel;

    /**
     * @param Quote $quoteViewModel
     */
    public function __construct(Quote $quoteViewModel)
    {
        $this->quoteViewModel = $quoteViewModel;
    }

    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        /** @var QuoteInterface $quote */
        $quote = $variables[EmailVariables::QUOTE];
        $variables[EmailVariables::TOTAL] = $this->quoteViewModel->getQuoteTotalFormatted($quote->getBaseQuoteTotal());

        return $variables;
    }
}
