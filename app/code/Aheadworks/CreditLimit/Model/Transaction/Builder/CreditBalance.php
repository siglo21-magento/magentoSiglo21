<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Builder;

use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\Source\Transaction\Action as TransactionActionSource;
use Aheadworks\CreditLimit\Model\Transaction\TransactionBuilderInterface;
use Aheadworks\CreditLimit\Model\Transaction\CreditSummaryManagement;
use Aheadworks\CreditLimit\Model\Transaction\Balance\Calculator as BalanceCalculator;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Model\Website\CurrencyList;

/**
 * Class CreditBalance
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Builder
 */
class CreditBalance extends AbstractBuilder implements TransactionBuilderInterface
{
    /**
     * @var BalanceCalculator
     */
    private $balanceCalculator;

    /**
     * @var CurrencyList
     */
    private $currencyList;

    /**
     * @param TransactionActionSource $transactionActionSource
     * @param CreditSummaryManagement $summaryManagement
     * @param BalanceCalculator $balanceCalculator
     * @param CurrencyList $currencyList
     */
    public function __construct(
        TransactionActionSource $transactionActionSource,
        CreditSummaryManagement $summaryManagement,
        BalanceCalculator $balanceCalculator,
        CurrencyList $currencyList
    ) {
        parent::__construct($transactionActionSource, $summaryManagement);
        $this->balanceCalculator = $balanceCalculator;
        $this->currencyList = $currencyList;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function checkIsValid(TransactionParametersInterface $params)
    {
        $updateBalanceActions = $this->transactionActionSource->getActionsToUpdateCreditBalance();


        if (!in_array($params->getAction(), $updateBalanceActions)) {
            return false;
        }

        if ($params->getAmount() === null) {
            throw new \InvalidArgumentException(__('Amount is required'));
        }
        if (!is_numeric($params->getAmount())) {
            throw new LocalizedException(__('Amount value is not correct'));
        }

        $summary = $this->summaryManagement->getCreditSummary($params->getCustomerId(), true);
        if (!$params->isAllowedToExceedLimit() && $this->checkIsCreditLimitExceeded($params, $summary)) {
            throw new LocalizedException(
                __(
                    'This request cannot be processed '
                    . 'because your order amount exceeds your available credit amount'
                )
            );
        }

        if ($params->getUsedCurrency()) {
            $allowedCurrencies = $this->currencyList->getAllowedCurrenciesForWebsite($summary->getWebsiteId());
            if (!in_array($params->getUsedCurrency(), $allowedCurrencies)) {
                throw new LocalizedException(
                    __('Currency %1 is not allowed', $params->getUsedCurrency())
                );
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function build(TransactionInterface $transaction, TransactionParametersInterface $params)
    {
        $summary = $this->summaryManagement->getCreditSummary($params->getCustomerId());
        $creditLimit = $summary->getCreditLimit();
        $amountCurrency = $params->getAmountCurrency() ?? $summary->getCurrency();
        $usedCurrency = $params->getUsedCurrency() ?? $summary->getCurrency();

        $creditBalance = $this->balanceCalculator->calculateCreditBalance(
            $summary->getCreditBalance(),
            $summary->getCurrency(),
            $params->getAmount(),
            $amountCurrency
        );
        $availableCredit = $this->balanceCalculator->calculateAvailableCredit(
            $creditBalance,
            $creditLimit
        );

        $summary->setCreditBalance($creditBalance);
        if ($params->getAmount() > 0) {
            $today = new \DateTime('now', new \DateTimeZone('UTC'));
            $lastPaymentDate = $today->format(StdlibDateTime::DATETIME_PHP_FORMAT);
            $summary->setLastPaymentDate($lastPaymentDate);
        }
        if (!$summary->getIsCustomCreditLimit()) {
            $summary->setCreditLimit(null);
        }
        $summary = $this->summaryManagement->saveCreditSummary($summary);

        $transaction->setCreditBalance($creditBalance);
        $transaction->setAmount($params->getAmount());
        $transaction->setCreditCurrency($summary->getCurrency());
        $transaction->setActionCurrency($usedCurrency);

        if ($amountCurrency != $usedCurrency) {
            $transaction->setRateToCreditCurrency(
                $this->balanceCalculator->calculateRate($amountCurrency, $summary->getCurrency())
            );
            $transaction->setRateToActionCurrency(
                $this->balanceCalculator->calculateRate($amountCurrency, $usedCurrency)
            );
        } else {
            $transaction->setRateToActionCurrency(
                $this->balanceCalculator->calculateRate($summary->getCurrency(), $usedCurrency)
            );
        }

        $transaction->setCreditAvailable($availableCredit);
        $transaction->setCreditLimit($creditLimit);
        $transaction->setSummaryId($summary->getSummaryId());
    }

    /**
     * Check is credit limit is exceeded
     *
     * @param TransactionParametersInterface $params
     * @param SummaryInterface $summary
     * @return bool
     * @throws \Exception
     */
    private function checkIsCreditLimitExceeded(TransactionParametersInterface $params, SummaryInterface $summary)
    {
        $reimbursedAmountActions = $this->transactionActionSource->getActionsToReimburseBalance();
        if (in_array($params->getAction(), $reimbursedAmountActions)) {
            return false;
        }

        $amountCurrency = $params->getAmountCurrency() ?? $summary->getCurrency();

        // Used balance calculator to calculate exceeded amount
        $result = $this->balanceCalculator->calculateCreditBalance(
            $summary->getCreditAvailable(),
            $summary->getCurrency(),
            $params->getAmount(),
            $amountCurrency
        );

        // adrian.olave@gmail.com
        // To convert this minus number into a positive number, delete limit Exceeded.
        $result = abs($result);

        return $result < 0;
    }
}
