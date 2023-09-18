<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\CreditSummary;
use Aheadworks\CreditLimit\Model\ResourceModel\CustomerGroupConfig;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction;
use Aheadworks\CreditLimit\Model\ResourceModel\Job;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 *
 * @package Aheadworks\CreditLimit\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @inheritdoc
     *
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->createCustomerGroupCreditLimitTable($installer);
        $this->createCreditSummaryTable($installer);
        $this->createTransactionTable($installer);
        $this->createTransactionEntityTable($installer);
        $this->createJobTable($installer);

        $installer->endSetup();
    }

    /**
     * Add customer group credit limit table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createCustomerGroupCreditLimitTable(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_cl_customer_group_credit_limit'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable(CustomerGroupConfig::MAIN_TABLE_NAME))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )->addColumn(
                'customer_group_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer Group ID'
            )->addColumn(
                'website_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website ID'
            )->addColumn(
                'credit_limit',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credit limit'
            )->addIndex(
                $installer->getIdxName(CustomerGroupConfig::MAIN_TABLE_NAME, ['customer_group_id']),
                ['customer_group_id']
            )->addIndex(
                $installer->getIdxName(CustomerGroupConfig::MAIN_TABLE_NAME, ['website_id']),
                ['website_id']
            )->addForeignKey(
                $installer->getFkName(
                    CustomerGroupConfig::MAIN_TABLE_NAME,
                    'customer_group_id',
                    'customer_group',
                    'customer_group_id'
                ),
                'customer_group_id',
                $installer->getTable('customer_group'),
                'customer_group_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    CustomerGroupConfig::MAIN_TABLE_NAME,
                    'website_id',
                    'store_website',
                    'website_id'
                ),
                'website_id',
                $installer->getTable('store_website'),
                'website_id',
                Table::ACTION_CASCADE
            )->setComment('Customer Group Credit Limit table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add credit summary table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createCreditSummaryTable(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_cl_credit_summary'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable(CreditSummary::MAIN_TABLE_NAME))
            ->addColumn(
                SummaryInterface::SUMMARY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Summary ID'
            )->addColumn(
                SummaryInterface::WEBSITE_ID,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Website ID'
            )->addColumn(
                SummaryInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer ID'
            )->addColumn(
                SummaryInterface::CREDIT_LIMIT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Credit Limit'
            )->addColumn(
                SummaryInterface::CREDIT_BALANCE,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credit Balance'
            )->addColumn(
                SummaryInterface::CURRENCY,
                Table::TYPE_TEXT,
                3,
                ['nullable' => false],
                'Credit Limit Currency'
            )->addColumn(
                SummaryInterface::LAST_PAYMENT_DATE,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Last Payment Date'
            )->addColumn(
                SummaryInterface::COMPANY_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Company ID'
            )->addIndex(
                $installer->getIdxName(CreditSummary::MAIN_TABLE_NAME, [SummaryInterface::CUSTOMER_ID]),
                [SummaryInterface::CUSTOMER_ID]
            )->addIndex(
                $installer->getIdxName(CreditSummary::MAIN_TABLE_NAME, [SummaryInterface::WEBSITE_ID]),
                [SummaryInterface::WEBSITE_ID]
            )->addForeignKey(
                $installer->getFkName(
                    CreditSummary::MAIN_TABLE_NAME,
                    SummaryInterface::CUSTOMER_ID,
                    'customer_entity',
                    'entity_id'
                ),
                SummaryInterface::CUSTOMER_ID,
                $installer->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    CreditSummary::MAIN_TABLE_NAME,
                    SummaryInterface::WEBSITE_ID,
                    'store_website',
                    SummaryInterface::WEBSITE_ID
                ),
                SummaryInterface::WEBSITE_ID,
                $installer->getTable('store_website'),
                SummaryInterface::WEBSITE_ID,
                Table::ACTION_SET_NULL
            )->setComment('Credit Summary Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add transaction table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTransactionTable(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_cl_transaction'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable(Transaction::MAIN_TABLE_NAME))
            ->addColumn(
                TransactionInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Transaction ID'
            )->addColumn(
                TransactionInterface::SUMMARY_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Summary ID'
            )->addColumn(
                TransactionInterface::COMPANY_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Company ID'
            )->addColumn(
                TransactionInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                TransactionInterface::ACTION,
                Table::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Action'
            )->addColumn(
                TransactionInterface::AMOUNT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Amount'
            )->addColumn(
                TransactionInterface::CREDIT_LIMIT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credit Limit'
            )->addColumn(
                TransactionInterface::CREDIT_BALANCE,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credit Balance'
            )->addColumn(
                TransactionInterface::CREDIT_AVAILABLE,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credit Available'
            )->addColumn(
                TransactionInterface::CREDIT_CURRENCY,
                Table::TYPE_TEXT,
                3,
                ['nullable' => false],
                'Credit Currency'
            )->addColumn(
                TransactionInterface::ACTION_CURRENCY,
                Table::TYPE_TEXT,
                3,
                ['nullable' => false],
                'Action Currency'
            )->addColumn(
                TransactionInterface::RATE_TO_CREDIT_CURRENCY,
                Table::TYPE_DECIMAL,
                '24,12',
                ['nullable' => true, 'default' => '0'],
                'Rate to Credit Currency'
            )->addColumn(
                TransactionInterface::RATE_TO_ACTION_CURRENCY,
                Table::TYPE_DECIMAL,
                '24,12',
                ['nullable' => false, 'default' => '0'],
                'Rate to Action Currency'
            )->addColumn(
                TransactionInterface::PO_NUMBER,
                Table::TYPE_TEXT,
                32,
                ['nullable' => true],
                'Purchase Order Number'
            )->addColumn(
                TransactionInterface::UPDATED_BY,
                Table::TYPE_TEXT,
                150,
                ['nullable' => true],
                'Updated By'
            )->addColumn(
                TransactionInterface::COMMENT_TO_CUSTOMER,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Comment to Customer'
            )->addColumn(
                TransactionInterface::COMMENT_TO_CUSTOMER_PLACEHOLDER,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Comment to Customer Placeholder'
            )->addColumn(
                TransactionInterface::COMMENT_TO_ADMIN,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Comment to Admin'
            )->addIndex(
                $installer->getIdxName(Transaction::MAIN_TABLE_NAME, [TransactionInterface::SUMMARY_ID]),
                [TransactionInterface::SUMMARY_ID]
            )->addForeignKey(
                $installer->getFkName(
                    Transaction::MAIN_TABLE_NAME,
                    TransactionInterface::SUMMARY_ID,
                    CreditSummary::MAIN_TABLE_NAME,
                    SummaryInterface::SUMMARY_ID
                ),
                TransactionInterface::SUMMARY_ID,
                $installer->getTable(CreditSummary::MAIN_TABLE_NAME),
                SummaryInterface::SUMMARY_ID,
                Table::ACTION_CASCADE
            )->setComment('Credit Limit Transaction Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add transaction entity table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTransactionEntityTable(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_cl_transaction_entity'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable(Transaction::TRANSACTION_ENTITY_TABLE))
            ->addColumn(
                TransactionEntityInterface::TRANSACTION_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Transaction ID'
            )->addColumn(
                TransactionEntityInterface::ENTITY_TYPE,
                Table::TYPE_TEXT,
                150,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity Type'
            )->addColumn(
                TransactionEntityInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn(
                TransactionEntityInterface::ENTITY_LABEL,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Entity Label'
            )->addIndex(
                $installer->getIdxName(
                    Transaction::TRANSACTION_ENTITY_TABLE,
                    [
                        TransactionEntityInterface::TRANSACTION_ID,
                        TransactionEntityInterface::ENTITY_TYPE,
                        TransactionEntityInterface::ENTITY_ID
                    ]
                ),
                [
                    TransactionEntityInterface::TRANSACTION_ID,
                    TransactionEntityInterface::ENTITY_TYPE,
                    TransactionEntityInterface::ENTITY_ID
                ]
            )->addForeignKey(
                $installer->getFkName(
                    Transaction::TRANSACTION_ENTITY_TABLE,
                    TransactionEntityInterface::TRANSACTION_ID,
                    Transaction::MAIN_TABLE_NAME,
                    TransactionInterface::ID
                ),
                TransactionEntityInterface::TRANSACTION_ID,
                $installer->getTable(Transaction::MAIN_TABLE_NAME),
                TransactionInterface::ID,
                Table::ACTION_CASCADE
            )->setComment('Credit Limit Transaction Entity Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add job table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createJobTable(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_cl_async_job'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable(Job::MAIN_TABLE_NAME))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Job ID'
            )->addColumn(
                'type',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Job Type'
            )->addColumn(
                'configuration',
                Table::TYPE_TEXT,
                Table::DEFAULT_TEXT_SIZE,
                ['nullable' => false],
                'Job Name'
            )->addColumn(
                'status',
                Table::TYPE_TEXT,
                20,
                ['nullable' => false],
                'Job Status'
            )->setComment('Credit Limit Job Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }
}
