<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Balance;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;

/**
 * Class Formatter
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Balance
 */
class Formatter
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceFormatter;

    /**
     * @param PriceCurrencyInterface $priceFormatter
     */
    public function __construct(
        PriceCurrencyInterface $priceFormatter
    ) {
        $this->priceFormatter = $priceFormatter;
    }

    /**
     * Format transaction amount
     *
     * @param array $transactionData
     * @return string
     */
    public function formatTransactionAmount($transactionData)
    {
        $rateToActionCurrency = isset($transactionData[TransactionInterface::RATE_TO_ACTION_CURRENCY])
            ? (float)$transactionData[TransactionInterface::RATE_TO_ACTION_CURRENCY] : 1;
        $rateToCreditCurrency = isset($transactionData[TransactionInterface::RATE_TO_CREDIT_CURRENCY])
            ? (float)$transactionData[TransactionInterface::RATE_TO_CREDIT_CURRENCY] : 0;
        $amount = $transactionData[TransactionInterface::AMOUNT];

        if ($rateToCreditCurrency) {
            $creditAmountConverted = $amount * $rateToCreditCurrency;
            $actionAmountConverted = $amount * $rateToActionCurrency;
        } else {
            $actionAmountConverted = $amount;
            if ([TransactionInterface::CREDIT_CURRENCY] == [TransactionInterface::ACTION_CURRENCY]) {
                $creditAmountConverted = $amount;
            } else {
                $creditAmountConverted = $amount / $rateToActionCurrency;
            }
        }

        $creditCurrency = $transactionData[TransactionInterface::CREDIT_CURRENCY];
        if ($creditAmountConverted == $actionAmountConverted) {
            $result = $this->formatPrice($creditAmountConverted, $creditCurrency);
        } else {
            $actionFormattedPrice = $this->formatPrice(
                $actionAmountConverted,
                $transactionData[TransactionInterface::ACTION_CURRENCY]
            );
            $result = sprintf(
                '%s (%s)<br>%s/%s: %s',
                $this->formatPrice($creditAmountConverted, $creditCurrency),
                $actionFormattedPrice,
                $transactionData[TransactionInterface::CREDIT_CURRENCY],
                $transactionData[TransactionInterface::ACTION_CURRENCY],
                number_format($transactionData[TransactionInterface::RATE_TO_ACTION_CURRENCY], 4)
            );
        }

        return $result;
    }

    /**
     * Format price
     *
     * @param float $price
     * @param string $currency
     * @return string
     */
    private function formatPrice($price, $currency)
    {
        return $this->priceFormatter->format(
            $price,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            null,
            $currency
        );
    }
}
