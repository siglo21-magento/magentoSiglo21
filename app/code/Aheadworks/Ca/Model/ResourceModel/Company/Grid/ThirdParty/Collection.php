<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ResourceModel\Company\Grid\ThirdParty;

use Aheadworks\Ca\Model\ResourceModel\Company;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Collection
 * @package Aheadworks\Ca\Model\ResourceModel\Company\Grid\ThirdParty
 */
class Collection
{
    /**
     * @var string
     */
    private $relativeField;

    /**
     * @var string
     */
    private $mainTable;

    /**
     * @var array
     */
    private $additionalCompanyColumns = [
        'aw_ca_company' => 'awca_company.name'
    ];

    /**
     * @var array
     */
    private $additionalCompanyUserColumns = [
        'aw_ca_is_activated' => 'awca_company_user.is_activated'
    ];

    /**
     * @var array
     */
    private $additionalColumns;

    /**
     * @param string $relativeField
     * @param string $mainTable
     * @param array $additionalCompanyColumns
     * @param array $additionalCompanyUserColumns
     */
    public function __construct(
        $relativeField = '',
        $mainTable = 'main_table',
        $additionalCompanyColumns = [],
        $additionalCompanyUserColumns = []
    ) {
        $this->relativeField = $relativeField;
        $this->mainTable = $mainTable;
        $this->additionalCompanyColumns = array_merge($this->additionalCompanyColumns, $additionalCompanyColumns);
        $this->additionalCompanyUserColumns = array_merge(
            $this->additionalCompanyUserColumns,
            $additionalCompanyUserColumns
        );
        $this->additionalColumns = array_merge($this->additionalCompanyColumns, $this->additionalCompanyUserColumns);
    }

    /**
     * Join fields before load
     *
     * @param AbstractDb $collection
     * @return AbstractDb
     */
    public function joinFieldsBeforeLoad($collection)
    {
        if (!$collection->isLoaded()) {
            $this->joinFields($collection);
        }

        return $collection;
    }

    /**
     * Join fields
     *
     * @param AbstractDb $collection
     * @return void
     */
    private function joinFields($collection)
    {
        $select = $collection->getSelect();

        $select->joinLeft(
            ['awca_company_user' => $collection->getTable(CompanyUser::MAIN_TABLE_NAME)],
            'awca_company_user.customer_id = ' . $this->mainTable . '.' . $this->relativeField,
            $this->additionalCompanyUserColumns
        );
        $select->joinLeft(
            ['awca_company' => $collection->getTable(Company::MAIN_TABLE_NAME)],
            'awca_company_user.company_id = awca_company.id',
            $this->additionalCompanyColumns
        );

        foreach ($this->additionalColumns as $filter => $alias) {
            $collection->addFilterToMap($filter, $alias);
        }
    }

    /**
     * Add field to filter for third party collection
     *
     * @param AbstractDb $collection
     * @param string $field
     * @return AbstractDb
     */
    public function addFieldToFilter($collection, $field)
    {
        if (isset($this->additionalColumns[$field])) {
            $collection->addFilterToMap($field, $this->additionalColumns[$field]);
        }

        return $collection;
    }
}
