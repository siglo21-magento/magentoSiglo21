<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Balance;

use Aheadworks\CreditLimit\Model\Currency\RateConverter;

/**
 * Class Calculator
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Balance
 */
class Calculator
{
    /**
     * @var RateConverter
     */
    private $rateConverter;

    /**
     * @param RateConverter $rateConverter
     */
    public function __construct(
        RateConverter $rateConverter
    ) {
        $this->rateConverter = $rateConverter;
    }

    /**
     * Calculate new credit balance
     *
     * @param float $creditBalance
     * @param string $creditCurrency
     * @param float $amount
     * @param string $amountCurrency
     * @return float
     * @throws \Exception
     */
    public function calculateCreditBalance($creditBalance, $creditCurrency, $amount, $amountCurrency)
    {
        $amount = $this->rateConverter->convertAmount($amount, $amountCurrency, $creditCurrency);
        return $creditBalance + $amount;
    }

    /**
     * Calculate new available credit
     *
     * @param float $creditBalance
     * @param float $creditLimit
     * @return float
     */
    public function calculateAvailableCredit($creditBalance, $creditLimit)
    {
        return $creditBalance + $creditLimit;
    }

    /**
     * Calculate currency rate between two currencies
     *
     * @param string $currencyFrom
     * @param string $currencyTo
     * @return float
     */
    public function calculateRate($currencyFrom, $currencyTo)
    {
        return $this->rateConverter->getRate($currencyFrom, $currencyTo);
    }
}
