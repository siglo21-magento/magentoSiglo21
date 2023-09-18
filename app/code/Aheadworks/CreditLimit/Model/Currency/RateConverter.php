<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Currency;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Directory\Model\Currency;

/**
 * Class RateConverter
 *
 * @package Aheadworks\CreditLimit\Model\Currency
 */
class RateConverter
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Convert amount from one currency to another
     *
     * @param float $amount
     * @param string $currencyFrom
     * @param string $currencyTo
     * @return float
     * @throws \Exception
     */
    public function convertAmount($amount, $currencyFrom, $currencyTo)
    {
        if ($currencyFrom != $currencyTo) {
            /** @var Currency $actionCurrency */
            $actionCurrency = $this->priceCurrency->getCurrency(null, $currencyFrom);
            if ($actionCurrency->getRate($currencyTo)) {
                $amount = $this->priceCurrency->round($actionCurrency->convert($amount, $currencyTo));
            }
        }

        return $amount;
    }

    /**
     * Calculate currency rate between two currencies
     *
     * @param string $currencyFrom
     * @param string $currencyTo
     * @return float
     */
    public function getRate($currencyFrom, $currencyTo)
    {
        if ($currencyFrom == $currencyTo) {
            return 1;
        }
        /** @var Currency $creditCurrency */
        $creditCurrency = $this->priceCurrency->getCurrency(null, $currencyFrom);

        return $creditCurrency->getRate($currencyTo);
    }
}
