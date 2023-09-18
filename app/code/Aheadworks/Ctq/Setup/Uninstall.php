<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Setup;

use Aheadworks\Ctq\Model\ResourceModel\Comment;
use Aheadworks\Ctq\Model\ResourceModel\History;
use Aheadworks\Ctq\Model\ResourceModel\Quote;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class Uninstall
 * @package Aheadworks\Ctq\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $dataSetup;

    /**
     * @param QuoteSetupFactory $setupFactory
     * @param ModuleDataSetupInterface $dataSetup
     */
    public function __construct(
        QuoteSetupFactory $setupFactory,
        ModuleDataSetupInterface $dataSetup
    ) {
        $this->quoteSetupFactory = $setupFactory;
        $this->dataSetup = $dataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this
            ->uninstallTables($installer)
            ->uninstallConfigData($installer)
            ->uninstallFlagData($installer);

        $installer->endSetup();
    }

    /**
     * Uninstall all module tables
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallTables(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $connection->dropTable($installer->getTable(Quote::MAIN_TABLE_NAME));
        $connection->dropTable($installer->getTable(Comment::MAIN_TABLE_NAME));
        $connection->dropTable($installer->getTable(Comment::ATTACHMENT_TABLE_NAME));
        $connection->dropTable($installer->getTable(History::MAIN_TABLE_NAME));

        return $this;
    }

    /**
     * Uninstall module data from config
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallConfigData(SchemaSetupInterface $installer)
    {
        $configTable = $installer->getTable('core_config_data');
        $installer->getConnection()->delete($configTable, "`path` LIKE 'aw_ctq%'");

        return $this;
    }

    /**
     * Uninstall module data from flag table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallFlagData(SchemaSetupInterface $installer)
    {
        $flagTable = $installer->getTable('flag');
        $installer->getConnection()->delete($flagTable, "`flag_code` LIKE 'aw_ctq%'");

        return $this;
    }
}
