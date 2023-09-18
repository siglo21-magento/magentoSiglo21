<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Setup;

use Aheadworks\Ctq\Model\ResourceModel\History;
use Aheadworks\Ctq\Model\ResourceModel\Quote;
use Aheadworks\Ctq\Model\ResourceModel\Comment;
use Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder\Status as ReminderStatus;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\Ctq\Setup\Updater\Schema\Updater as SchemaUpdater;

/**
 * Class InstallSchema
 * @package Aheadworks\Ctq\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var SchemaUpdater
     */
    private $updater;

    /**
     * @param SchemaUpdater $updater
     */
    public function __construct(
        SchemaUpdater $updater
    ) {
        $this->updater = $updater;
    }
    
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this
            ->createQuoteTable($installer)
            ->addCommentTables($installer)
            ->addHistoryTables($installer);

        $this->updater->update130($setup);

        $installer->endSetup();
    }

    /**
     * Create table 'aw_ctq_quote'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createQuoteTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(Quote::MAIN_TABLE_NAME))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Quote Id'
            )->addColumn(
                'cart_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Cart Id'
            )->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'last_updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Last Updated At'
            )->addColumn(
                'status',
                Table::TYPE_TEXT,
                80,
                ['nullable' => false],
                'Status'
            )->addColumn(
                'seller_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Seller ID'
            )->addColumn(
                'cc_email_receiver',
                Table::TYPE_TEXT,
                100,
                ['nullable' => true],
                'CC email receiver'
            )->addColumn(
                'reminder_date',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Reminder Date'
            )->addColumn(
                'expiration_date',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Expiration Date'
            )->addColumn(
                'reminder_status',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'default' => ReminderStatus::READY_TO_BE_SENT],
                'Reminder Status'
            )->addColumn(
                'cart',
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Quote Snapshot'
            )->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )->addColumn(
                'negotiated_discount_type',
                Table::TYPE_TEXT,
                40,
                ['nullable' => true],
                'Negotiated Discount Type'
            )->addColumn(
                'negotiated_discount_value',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Negotiated Discount Value'
            )->addColumn(
                'base_quote_total',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Base Quote Total'
            )->addColumn(
                'quote_total',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Quote Total'
            )->addColumn(
                'base_quote_total_negotiated',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Base Quote Total Negotiated'
            )->addColumn(
                'quote_total_negotiated',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Quote Total Negotiated'
            )->addIndex(
                $setup->getIdxName(Quote::MAIN_TABLE_NAME, ['cart_id']),
                ['cart_id']
            )->addIndex(
                $setup->getIdxName(Quote::MAIN_TABLE_NAME, ['customer_id']),
                ['customer_id']
            )->setComment('AW Ctq Quote Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add message tables
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addCommentTables(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(Comment::MAIN_TABLE_NAME))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Comment Id'
            )->addColumn(
                'quote_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Quote Id'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'comment',
                Table::TYPE_TEXT,
                '1M',
                ['nullable' => false],
                'comment'
            )->addColumn(
                'owner_type',
                Table::TYPE_TEXT,
                15,
                ['nullable' => false],
                'Owner Type'
            )->addColumn(
                'owner_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Owner Id'
            )->addIndex(
                $setup->getIdxName(Comment::MAIN_TABLE_NAME, ['quote_id']),
                ['quote_id']
            )->addIndex(
                $setup->getIdxName(Comment::MAIN_TABLE_NAME, ['owner_id']),
                ['owner_id']
            )->addForeignKey(
                $setup->getFkName(Comment::MAIN_TABLE_NAME, 'quote_id', Quote::MAIN_TABLE_NAME, 'id'),
                'quote_id',
                $setup->getTable(Quote::MAIN_TABLE_NAME),
                'id',
                Table::ACTION_CASCADE
            )->setComment('AW Ctq Comment');
        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()
            ->newTable($setup->getTable(Comment::ATTACHMENT_TABLE_NAME))
            ->addColumn(
                'comment_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Comment Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                Table::DEFAULT_TEXT_SIZE,
                ['nullable' => false],
                'Name'
            )->addColumn(
                'file_name',
                Table::TYPE_TEXT,
                Table::DEFAULT_TEXT_SIZE,
                ['nullable' => false],
                'File Name On The Server'
            )->addForeignKey(
                $setup->getFkName(Comment::ATTACHMENT_TABLE_NAME, 'comment_id', Comment::MAIN_TABLE_NAME, 'id'),
                'comment_id',
                $setup->getTable(Comment::MAIN_TABLE_NAME),
                'id',
                Table::ACTION_CASCADE
            )->addIndex(
                $setup->getIdxName(Comment::ATTACHMENT_TABLE_NAME, ['comment_id']),
                ['comment_id']
            )->setComment('Aw Ctq Attachments');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add history tables
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addHistoryTables(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(History::MAIN_TABLE_NAME))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'History Id'
            )->addColumn(
                'quote_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Quote Id'
            )->addColumn(
                'status',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Action'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'owner_type',
                Table::TYPE_TEXT,
                15,
                ['nullable' => false],
                'Owner Type'
            )->addColumn(
                'owner_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Owner Id'
            )->addColumn(
                'actions',
                Table::TYPE_TEXT,
                '1M',
                ['nullable' => false],
                'Actions'
            )->addIndex(
                $setup->getIdxName(History::MAIN_TABLE_NAME, ['quote_id']),
                ['quote_id']
            )->addIndex(
                $setup->getIdxName(History::MAIN_TABLE_NAME, ['owner_id']),
                ['owner_id']
            )->addForeignKey(
                $setup->getFkName(History::MAIN_TABLE_NAME, 'quote_id', Quote::MAIN_TABLE_NAME, 'id'),
                'quote_id',
                $setup->getTable(Quote::MAIN_TABLE_NAME),
                'id',
                Table::ACTION_CASCADE
            )->setComment('AW Ctq History');
        $setup->getConnection()->createTable($table);

        return $this;
    }
}
