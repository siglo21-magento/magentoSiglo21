<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Setup;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\Data\GroupInterface;
use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Model\ResourceModel\Company;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser;
use Aheadworks\Ca\Model\ResourceModel\Group;
use Aheadworks\Ca\Model\ResourceModel\Role;
use Aheadworks\Ca\Model\Source\Company\Status;
use Aheadworks\Ca\Setup\Updater\Schema\Updater;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Aheadworks\Ca\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Updater
     */
    private $updater;

    /**
     * @param Updater $updater
     */
    public function __construct(
        Updater $updater
    ) {
        $this->updater = $updater;
    }

    /**
     * {@inheritdoc}
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this
            ->createCompanyTable($installer)
            ->createGroupTable($installer)
            ->createRoleTable($installer)
            ->createCompanyUserTable($installer);
        $this->updater
            ->install110($setup)
            ->install130($setup);

        $installer->endSetup();
    }

    /**
     * Create table 'aw_ca_company'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createCompanyTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(Company::MAIN_TABLE_NAME))
            ->addColumn(
                CompanyInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Company Id'
            )
            ->addColumn(
                CompanyInterface::ROOT_GROUP_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Root group id'
            )
            ->addColumn(
                CompanyInterface::STATUS,
                Table::TYPE_TEXT,
                20,
                ['nullable' => false, 'default' => Status::PENDING_APPROVAL],
                'Status'
            )
            ->addColumn(
                CompanyInterface::NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )
            ->addColumn(
                CompanyInterface::LEGAL_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Legal name'
            )
            ->addColumn(
                CompanyInterface::EMAIL,
                Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'Email'
            )
            ->addColumn(
                CompanyInterface::TAX_ID,
                Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Tax id'
            )
            ->addColumn(
                CompanyInterface::RE_SELLER_ID,
                Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Seller id'
            )
            ->addColumn(
                CompanyInterface::STREET,
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Street'
            )
            ->addColumn(
                CompanyInterface::CITY,
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'City'
            )
            ->addColumn(
                CompanyInterface::COUNTRY_ID,
                Table::TYPE_TEXT,
                2,
                ['nullable' => false],
                'Country'
            )
            ->addColumn(
                CompanyInterface::REGION,
                Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'State'
            )
            ->addColumn(
                CompanyInterface::REGION_ID,
                Table::TYPE_INTEGER,
                10,
                ['nullable' => true, 'unsigned' => true],
                'State Id'
            )
            ->addColumn(
                CompanyInterface::POSTCODE,
                Table::TYPE_TEXT,
                30,
                ['nullable' => false],
                'Postcode'
            )
            ->addColumn(
                CompanyInterface::TELEPHONE,
                Table::TYPE_TEXT,
                30,
                ['nullable' => true],
                'Telephone'
            )
            ->addColumn(
                CompanyInterface::NOTES,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Notes'
            )
            ->addColumn(
                CompanyInterface::SALES_REPRESENTATIVE_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Sales Representative ID'
            )
            ->addColumn(
                CompanyInterface::CUSTOMER_GROUP_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Customer Group ID'
            )
            ->addColumn(
                CompanyInterface::IS_ALLOWED_TO_QUOTE,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Allowed to Quote'
            )
            ->addColumn(
                CompanyInterface::IS_APPROVED_NOTIFICATION_SENT,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Approved Notification Sent'
            )
            ->addColumn(
                CompanyInterface::IS_DECLINED_NOTIFICATION_SENT,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Declined Notification Sent'
            )
            ->addColumn(
                CompanyInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                CompanyInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )
            ->addIndex(
                $setup->getIdxName(Company::MAIN_TABLE_NAME, [CompanyInterface::EMAIL]),
                [CompanyInterface::EMAIL]
            )
            ->addIndex(
                $setup->getIdxName(Company::MAIN_TABLE_NAME, [CompanyInterface::ROOT_GROUP_ID]),
                [CompanyInterface::ROOT_GROUP_ID]
            )
            ->addIndex(
                $setup->getIdxName(
                    Company::MAIN_TABLE_NAME,
                    [CompanyInterface::NAME, CompanyInterface::LEGAL_NAME],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [CompanyInterface::NAME, CompanyInterface::LEGAL_NAME],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )
            ->setComment('AW Company Table');

        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_ca_group'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createGroupTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(Group::MAIN_TABLE_NAME))
            ->addColumn(
                GroupInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Group Id'
            )
            ->addColumn(
                GroupInterface::PARENT_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Parent group id'
            )
            ->addColumn(
                GroupInterface::PATH,
                Table::TYPE_TEXT,
                200,
                ['nullable' => false],
                'Path Tree'
            )
            ->addColumn(
                GroupInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                GroupInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )
            ->addIndex(
                $setup->getIdxName(Group::MAIN_TABLE_NAME, [GroupInterface::PARENT_ID]),
                [GroupInterface::PARENT_ID]
            )
            ->setComment('AW Group Table');

        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_ca_role'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createRoleTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(Role::MAIN_TABLE_NAME))
            ->addColumn(
                RoleInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Role Id'
            )
            ->addColumn(
                RoleInterface::COMPANY_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Company Id'
            )
            ->addColumn(
                RoleInterface::NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Role Name'
            )
            ->addColumn(
                RoleInterface::PERMISSIONS,
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Permissions'
            )
            ->addColumn(
                RoleInterface::AW_STC_BASE_AMOUNT_LIMIT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'AW Store Credit Base Amount Limit'
            )
            ->addColumn(
                RoleInterface::AW_RP_BASE_AMOUNT_LIMIT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'AW Reward Points Base Amount Limit'
            )
            ->addIndex(
                $setup->getIdxName(Role::MAIN_TABLE_NAME, [RoleInterface::COMPANY_ID]),
                [RoleInterface::COMPANY_ID]
            )
            ->addForeignKey(
                $setup->getFkName(
                    Role::MAIN_TABLE_NAME,
                    RoleInterface::COMPANY_ID,
                    Company::MAIN_TABLE_NAME,
                    CompanyInterface::ID
                ),
                RoleInterface::COMPANY_ID,
                $setup->getTable(Company::MAIN_TABLE_NAME),
                CompanyInterface::ID,
                Table::ACTION_CASCADE
            )
            ->setComment('AW Role Table');

        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_ca_company_user'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createCompanyUserTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(CompanyUser::MAIN_TABLE_NAME))
            ->addColumn(
                CompanyUserInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Customer Id'
            )
            ->addColumn(
                CompanyUserInterface::COMPANY_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Company Id'
            )
            ->addColumn(
                CompanyUserInterface::IS_ROOT,
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'default' => 0],
                'Is root in company'
            )
            ->addColumn(
                CompanyUserInterface::IS_ACTIVATED,
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'default' => 1],
                'Is user activated'
            )
            ->addColumn(
                CompanyUserInterface::COMPANY_GROUP_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Company Group Id'
            )
            ->addColumn(
                CompanyUserInterface::COMPANY_ROLE_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Company Role Id'
            )
            ->addColumn(
                CompanyUserInterface::JOB_TITLE,
                Table::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Job Title'
            )
            ->addColumn(
                CompanyUserInterface::TELEPHONE,
                Table::TYPE_TEXT,
                30,
                ['nullable' => true],
                'Telephone'
            )
            ->addIndex(
                $setup->getIdxName(CompanyUser::MAIN_TABLE_NAME, [CompanyUserInterface::COMPANY_ID]),
                [CompanyUserInterface::COMPANY_ID]
            )
            ->addIndex(
                $setup->getIdxName(CompanyUser::MAIN_TABLE_NAME, [CompanyUserInterface::CUSTOMER_ID]),
                [CompanyUserInterface::CUSTOMER_ID]
            )
            ->addIndex(
                $setup->getIdxName(CompanyUser::MAIN_TABLE_NAME, [CompanyUserInterface::COMPANY_ROLE_ID]),
                [CompanyUserInterface::COMPANY_ROLE_ID]
            )
            ->addIndex(
                $setup->getIdxName(CompanyUser::MAIN_TABLE_NAME, [CompanyUserInterface::COMPANY_GROUP_ID]),
                [CompanyUserInterface::COMPANY_GROUP_ID]
            )
            ->addForeignKey(
                $setup->getFkName(
                    CompanyUser::MAIN_TABLE_NAME,
                    CompanyUserInterface::CUSTOMER_ID,
                    'customer_entity',
                    'entity_id'
                ),
                CompanyUserInterface::CUSTOMER_ID,
                $setup->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Company User Table');

        $setup->getConnection()->createTable($table);

        return $this;
    }
}
