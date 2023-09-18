<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\App\ResourceConnection;

/**
 * Class CustomerExtendedSql
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel
 */
class CustomerExtendedSql
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Apply columns to provided select
     *
     * @param Select $sqlSelect
     */
    public function applyColumns($sqlSelect)
    {
        $creditBalanceColumn = 'COALESCE(cl_customer_value.credit_balance, 0)';

        $columns = [
            'summary_id' => 'cl_customer_value.summary_id',
            'customer_id' => 'e.entity_id',
            'group_id' => 'e.group_id',
            'website_id' => 'e.website_id',
            'customer_name' => 'CONCAT(e.firstname, " ", e.lastname)',
            'customer_email' => 'e.email',
            'credit_limit' => $this->getCreditLimitColumn('null'),
            'currency' => 'cl_customer_value.currency',
            'is_custom_credit_limit' => 'IF(cl_customer_value.credit_limit IS NOT NULL, 1, 0)',
            'credit_balance' => $creditBalanceColumn,
            'credit_available' => '(' . $this->getCreditLimitColumn('0') . '+' . $creditBalanceColumn . ')',
            'last_payment_date' => 'cl_customer_value.last_payment_date',
            'company_id' => 'cl_customer_value.company_id',
        ];

        $sqlSelect->columns($columns);
    }

    /**
     * Apply joins to provided select
     *
     * @param Select $sqlSelect
     */
    public function applyJoins($sqlSelect)
    {
        $customerValueJoinCondition = [
            'cl_customer_value.customer_id = e.entity_id',
            'cl_customer_value.website_id = e.website_id',
        ];

        $sqlSelect->joinLeft(
            ['cl_website_value' => $this->resource->getTableName(CustomerGroupConfig::MAIN_TABLE_NAME)],
            implode(' AND ', $this->getCustomerGroupJoinCondition('cl_website_value', false)),
            []
        )->joinLeft(
            ['cl_default_value' => $this->resource->getTableName(CustomerGroupConfig::MAIN_TABLE_NAME)],
            implode(' AND ', $this->getCustomerGroupJoinCondition('cl_default_value', true)),
            []
        )->joinLeft(
            ['cl_customer_value' => $this->resource->getTableName(CreditSummary::MAIN_TABLE_NAME)],
            implode(' AND ', $customerValueJoinCondition),
            []
        );
    }

    /**
     * Get customer group join condition
     *
     * @param string $tableAlias
     * @param bool $isDefaultWebsite
     * @return array
     */
    private function getCustomerGroupJoinCondition($tableAlias, $isDefaultWebsite = false)
    {
        $websiteValue = $isDefaultWebsite ? '0' : 'e.website_id';
        return [
            $tableAlias . '.customer_group_id = e.group_id',
            $tableAlias . '.website_id = ' . $websiteValue
        ];
    }

    /**
     * Get credit limit column
     *
     * @param string $defaultValue
     * @return string
     */
    private function getCreditLimitColumn($defaultValue)
    {
        return sprintf(
            'COALESCE(cl_customer_value.credit_limit, cl_website_value.credit_limit, ' .
            'cl_default_value.credit_limit, %s)',
            $defaultValue
        );
    }
}
