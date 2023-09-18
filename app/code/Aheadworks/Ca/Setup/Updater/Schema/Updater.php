<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Setup\Updater\Schema;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Model\ResourceModel\Company;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser;
use Aheadworks\Ca\Model\ResourceModel\Role;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class Updater
 *
 * @package Aheadworks\Ca\Setup\Updater\Schema
 */
class Updater
{
    /**
     * Upgrade to version 1.1.0
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    public function upgradeTo110(SchemaSetupInterface $installer)
    {
        $this
            ->addAllowedPaymentMethodsTable($installer)
            ->addIsDefaultRoleField($installer)
            ->updateRootRoleToDefault($installer);

        return $this;
    }

    /**
     * Upgrade to version 1.3.0
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    public function install130(SchemaSetupInterface $installer)
    {
        $this->addCompanyListsTable($installer);

        return $this;
    }

    /**
     * Install version 1.1.0
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    public function install110(SchemaSetupInterface $installer)
    {
        $this
            ->addAllowedPaymentMethodsTable($installer)
            ->addIsDefaultRoleField($installer);

        return $this;
    }

    /**
     * Add Company Requisition Lists Table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addCompanyListsTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_ca_company_requisition_lists'))
            ->addColumn(
                'company_list_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Company Requisition List Id'
            )
            ->addColumn(
                'updated_by',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Updated By'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aw_ca_company_lists',
                    'updated_by',
                    'customer_entity',
                    'entity_id'
                ),
                'updated_by',
                $installer->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Company Requisition Lists');

        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add allowed payment methods table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addAllowedPaymentMethodsTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(Company::COMPANY_PAYMENTS_TABLE_NAME))
            ->addColumn(
                'company_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Company Id'
            )
            ->addColumn(
                'payment_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Payment Name'
            )
            ->addIndex(
                $installer->getIdxName(Company::COMPANY_PAYMENTS_TABLE_NAME, ['payment_name']),
                ['payment_name']
            )
            ->addForeignKey(
                $installer->getFkName(
                    Company::COMPANY_PAYMENTS_TABLE_NAME,
                    'id',
                    Company::MAIN_TABLE_NAME,
                    'company_id'
                ),
                'company_id',
                $installer->getTable(Company::MAIN_TABLE_NAME),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Company Allowed Payment Methods');

        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add allowed payment methods field
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addIsDefaultRoleField($installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(Role::MAIN_TABLE_NAME),
            RoleInterface::IS_DEFAULT,
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'after' => 'permissions',
                'default' => 0,
                'comment' => 'Is Default'
            ]
        );

        return $this;
    }

    /**
     * Update root role to default
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function updateRootRoleToDefault($installer)
    {
        $connection = $installer->getConnection();

        $select = $connection->select()->from(
            $installer->getTable(CompanyUser::MAIN_TABLE_NAME),
            [
                CompanyUserInterface::COMPANY_ROLE_ID
            ]
        )->where(CompanyUserInterface::IS_ROOT . ' = 1');
        $rootRoleIds = $connection->fetchCol($select);

        if (count($rootRoleIds)) {
            $connection->update(
                $installer->getTable(Role::MAIN_TABLE_NAME),
                [
                    RoleInterface::IS_DEFAULT => 1
                ],
                RoleInterface::ID . ' in (' . implode(',', array_values($rootRoleIds)) . ')'
            );
        }
        return $this;
    }
}
