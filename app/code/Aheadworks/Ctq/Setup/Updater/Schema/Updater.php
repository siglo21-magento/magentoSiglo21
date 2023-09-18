<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Setup\Updater\Schema;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\ResourceModel\Quote;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class Updater
 *
 * @package Aheadworks\Ctq\Setup\Updater\Schema
 */
class Updater
{
    /**
     * Update for 1.3.0 version
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function update130(SchemaSetupInterface $setup)
    {
        $this->addOrderIdColumn($setup);

        return $this;
    }

    /**
     * Add order id to quote table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addOrderIdColumn(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $tableName = $setup->getTable(Quote::MAIN_TABLE_NAME);
        if (!$connection->tableColumnExists($tableName, QuoteInterface::ORDER_ID)) {
            $connection->addColumn(
                $tableName,
                QuoteInterface::ORDER_ID,
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'unsigned' => true,
                    'after' => QuoteInterface::CART_ID,
                    'comment' => 'Order ID'
                ]
            );

            $connection->addIndex(
                $tableName,
                $connection->getIndexName($tableName, [QuoteInterface::ORDER_ID]),
                [QuoteInterface::ORDER_ID]
            );
        }

        return $this;
    }
}
