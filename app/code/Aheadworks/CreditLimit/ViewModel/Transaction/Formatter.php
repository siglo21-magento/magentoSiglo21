<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\ViewModel\Transaction;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Aheadworks\CreditLimit\Model\Source\Transaction\Action as ActionSource;
use Aheadworks\CreditLimit\Model\Transaction\Balance\Formatter as BalanceFormatter;

/**
 * Class Formatter
 *
 * @package Aheadworks\CreditLimit\ViewModel\Transaction
 */
class Formatter implements ArgumentInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var ActionSource
     */
    private $actionSource;

    /**
     * @var BalanceFormatter
     */
    private $balanceFormatter;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param ActionSource $actionSource
     * @param BalanceFormatter $balanceFormatter
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        ActionSource $actionSource,
        BalanceFormatter $balanceFormatter
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->actionSource = $actionSource;
        $this->balanceFormatter = $balanceFormatter;
    }

    /**
     * Format price
     *
     * @param float $price
     * @param string $currencyCode
     * @return string
     */
    public function formatPrice($price, $currencyCode)
    {
        return $this->priceCurrency->format(
            $price,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            null,
            $currencyCode
        );
    }

    /**
     * Format transaction action
     *
     * @param string $action
     * @return string
     */
    public function formatTransactionAction($action)
    {
        return $this->actionSource->getActionLabel($action);
    }

    /**
     * Format transaction amount
     *
     * @param array $transaction
     * @return string
     */
    public function formatTransactionAmount($transaction)
    {
        return $this->balanceFormatter->formatTransactionAmount($transaction);
    }
}
