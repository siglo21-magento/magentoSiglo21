<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Expiration\Notifier\VariableProcessor;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Email\VariableProcessorInterface;
use Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder\EmailVariables;

/**
 * Class ExpirationReminder
 *
 * @package Aheadworks\Ctq\Model\Quote\Expiration\Notifier\VariableProcessor
 */
class ExpirationReminder implements VariableProcessorInterface
{
    /**
     * @var VariableProcessorInterface[]
     */
    private $processors;

    /**
     * @param VariableProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Prepare variables
     *
     * @param array $variables
     * @return array
     */
    public function prepareVariables($variables)
    {
        /** @var QuoteInterface $quote */
        $quote = $variables[EmailVariables::QUOTE];
        $expirationDate = $quote->getExpirationDate();
        if ($quote->getExpirationDate()) {
            $expirationDate = new \DateTime($expirationDate, new \DateTimeZone('UTC'));
            $now = new \DateTime(null, new \DateTimeZone('UTC'));
            $now->setTime(00, 00, 00);
            $variables[EmailVariables::DAYS_NUMBER_UNTIL_EXPIRED] = $expirationDate->diff($now, 1)->format("%d");
        }

        return $variables;
    }
}
