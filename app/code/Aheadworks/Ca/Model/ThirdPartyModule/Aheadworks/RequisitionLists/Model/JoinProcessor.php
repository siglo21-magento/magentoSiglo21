<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model;

use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Collection;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class JoinProcessor
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model
 */
class JoinProcessor
{
    /**#@+
     * Table alias for join
     */
    const UPDATED_BY_CUSTOMER_ALIAS = 'u_customer';
    const OWNER_CUSTOMER_ALIAS = 'o_customer';
    /**#@-/

    /**
     * Join columns to main table
     *
     * @param Collection $collection
     */
    public function joinColumns($collection)
    {
        $this->joinUpdatedByColumn($collection);
        $this->joinOwnerColumn($collection);
    }

    /**
     * Join full name of user who last updated the list
     *
     * @param Collection $collection
     */
    private function joinUpdatedByColumn($collection)
    {
        $collection->getSelect()
            ->joinLeft(
                ['awcalist' => $collection->getTable('aw_ca_company_requisition_lists')],
                'awcalist.company_list_id = main_table.list_id',
                ['updated_by']
            )
            ->joinLeft(
                [self::UPDATED_BY_CUSTOMER_ALIAS => $collection->getTable('customer_entity')],
                'updated_by = ' . self::UPDATED_BY_CUSTOMER_ALIAS . '.entity_id',
                [
                    'u_fullname' => $this->getFullNameExpr($collection, self::UPDATED_BY_CUSTOMER_ALIAS)
                ]
            );
    }

    /**
     * Join full name of list owner
     *
     * @param Collection $collection
     */
    private function joinOwnerColumn($collection)
    {
        $collection->getSelect()
            ->joinLeft(
                [self::OWNER_CUSTOMER_ALIAS => $collection->getTable('customer_entity')],
                'main_table.customer_id = ' . self::OWNER_CUSTOMER_ALIAS . '.entity_id',
                [
                    'o_fullname' => $this->getFullNameExpr($collection, self::OWNER_CUSTOMER_ALIAS)
                ]
            );
    }

    /**
     * Get full name expr
     *
     * @param Collection $collection
     * @param string $tableAlias
     * @return \Zend_Db_Expr
     */
    private function getFullNameExpr($collection, $tableAlias)
    {
        return $collection->getConnection()->getConcatSql(
            [
                $tableAlias . '.' . CustomerInterface::FIRSTNAME,
                $tableAlias . '.' . CustomerInterface::LASTNAME
            ],
            ' '
        );
    }
}
