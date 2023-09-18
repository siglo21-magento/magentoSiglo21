<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\ResourceModel\Transaction;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\AbstractCollection;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction as ResourceTransaction;
use Aheadworks\CreditLimit\Model\Transaction;
use Aheadworks\CreditLimit\Model\ResourceModel\CreditSummary;

/**
 * Class Collection
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel\Transaction
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Transaction::class, ResourceTransaction::class);
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $filterMapping = [
            TransactionInterface::COMPANY_ID => 'main_table.' . TransactionInterface::COMPANY_ID,
            TransactionInterface::CREDIT_LIMIT => 'main_table.' . TransactionInterface::CREDIT_LIMIT,
            TransactionInterface::CREDIT_BALANCE => 'main_table.' . TransactionInterface::CREDIT_BALANCE
        ];
        foreach ($filterMapping as $filter => $alias) {
            $this->addFilterToMap($filter, $alias);
        }

        $this->getSelect()->joinLeft(
            ['aw_cl_summary_table' => $this->getTable(CreditSummary::MAIN_TABLE_NAME)],
            'main_table.summary_id = aw_cl_summary_table.summary_id',
            [SummaryInterface::CUSTOMER_ID, SummaryInterface::WEBSITE_ID]
        );
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            ResourceTransaction::TRANSACTION_ENTITY_TABLE,
            TransactionInterface::ID,
            TransactionEntityInterface::TRANSACTION_ID,
            [
                TransactionEntityInterface::ENTITY_TYPE,
                TransactionEntityInterface::ENTITY_ID,
                TransactionEntityInterface::ENTITY_LABEL
            ],
            'entities'
        );

        return parent::_afterLoad();
    }
}
