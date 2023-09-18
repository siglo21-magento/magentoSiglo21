<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Builder;

use Aheadworks\CreditLimit\Model\Source\Transaction\Action as TransactionActionSource;
use Aheadworks\CreditLimit\Model\Transaction\TransactionBuilderInterface;
use Aheadworks\CreditLimit\Model\Transaction\CreditSummaryManagement;
use Aheadworks\CreditLimit\Model\Transaction\Balance\Calculator as BalanceCalculator;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Model\Website\CurrencyList;

/**
 * Class CompanyBuilder
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Builder
 */
class CompanyBuilder extends AbstractBuilder implements TransactionBuilderInterface
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
     */
    public function checkIsValid(TransactionParametersInterface $params)
    {
        if ($params->getCompanyId() === null) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function build(TransactionInterface $transaction, TransactionParametersInterface $params)
    {
        $summary = $this->summaryManagement->getCreditSummary($params->getCustomerId());
        if (!$summary->getCompanyId()) {
            $summary->setCompanyId($params->getCompanyId());
            $this->summaryManagement->saveCreditSummary($summary);
        }

        $transaction->setSummaryId(null);
        $transaction->setCompanyId($params->getCompanyId());
    }
}
