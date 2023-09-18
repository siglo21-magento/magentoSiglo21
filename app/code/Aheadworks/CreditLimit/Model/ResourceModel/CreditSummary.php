<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\ResourceModel;

use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Framework\Model\ResourceModel\Db\Context as DbContext;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\DB\Select;

/**
 * Class CreditSummary
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel
 */
class CreditSummary extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_cl_credit_summary';

    /**
     * @var CustomerExtendedSql
     */
    protected $customerExtendedSql;

    /**
     * @param DbContext $context
     * @param EntityManager $entityManager
     * @param CustomerExtendedSql $customerExtendedSql
     * @param string|null $connectionName
     */
    public function __construct(
        DbContext $context,
        EntityManager $entityManager,
        CustomerExtendedSql $customerExtendedSql,
        $connectionName = null
    ) {
        $this->customerExtendedSql = $customerExtendedSql;
        parent::__construct($context, $entityManager, $connectionName);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, SummaryInterface::SUMMARY_ID);
    }

    /**
     * Get credit limit summary by customer ID
     *
     * @param int $customerId
     * @return array
     * @throws Select
     */
    public function loadByCustomerId($customerId)
    {
        if ($customerId) {
            $select = $this->prepareCreditSummaryGeneralSelect();
            $select->having(SummaryInterface::CUSTOMER_ID . ' = ?', $customerId);
            $havingCondition = [
                $this->getConnection()->quoteInto(SummaryInterface::CUSTOMER_ID . ' = ?', $customerId),
                SummaryInterface::SUMMARY_ID . ' IS NOT NULL'
            ];
            $select->orHaving(implode(' AND ', $havingCondition));
            $result = $this->getConnection()->fetchRow($select);
        } else {
            $result = [];
        }

        return $result;
    }

    /**
     * Prepare credit limit summary general select
     *
     * @return Select
     */
    public function prepareCreditSummaryGeneralSelect()
    {
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from(['e' => $this->getTable('customer_entity')], []);

        $this->customerExtendedSql->applyColumns($select);
        $this->customerExtendedSql->applyJoins($select);

        $havingCondition = [
            $connection->quoteInto(SummaryInterface::CREDIT_LIMIT . ' >= ?', 0),
            $connection->quoteInto(SummaryInterface::CREDIT_BALANCE . ' != ?', 0)
        ];

        $select->having(implode(' OR ', $havingCondition));

        return $select;
    }
}
