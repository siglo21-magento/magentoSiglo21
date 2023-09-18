<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Builder;

use Aheadworks\CreditLimit\Model\Source\Transaction\Action as TransactionActionSource;
use Aheadworks\CreditLimit\Model\Transaction\TransactionBuilderInterface;
use Aheadworks\CreditLimit\Model\Transaction\CreditSummaryManagement;

/**
 * Class AbstractBuilder
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Builder
 */
abstract class AbstractBuilder implements TransactionBuilderInterface
{
    /**
     * @var TransactionActionSource
     */
    protected $transactionActionSource;

    /**
     * @var CreditSummaryManagement
     */
    protected $summaryManagement;

    /**
     * @param TransactionActionSource $transactionActionSource
     * @param CreditSummaryManagement $summaryManagement
     */
    public function __construct(
        TransactionActionSource $transactionActionSource,
        CreditSummaryManagement $summaryManagement
    ) {
        $this->transactionActionSource = $transactionActionSource;
        $this->summaryManagement = $summaryManagement;
    }
}
