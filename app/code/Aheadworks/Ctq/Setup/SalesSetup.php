<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Setup;

use Magento\Sales\Setup\SalesSetup as MagentoSalesSetup;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\Ctq\Api\Data\CreditmemoInterface;
use Aheadworks\Ctq\Api\Data\CreditmemoItemInterface;
use Aheadworks\Ctq\Api\Data\InvoiceInterface;
use Aheadworks\Ctq\Api\Data\InvoiceItemInterface;
use Aheadworks\Ctq\Api\Data\OrderInterface;
use Aheadworks\Ctq\Api\Data\OrderItemInterface;

/**
 * Class SalesSetup
 *
 * @package Aheadworks\Ctq\Setup
 */
class SalesSetup extends MagentoSalesSetup
{
    /**
     * Retrieve attributes config to install
     *
     * @return array
     */
    public function getAttributesToInstall()
    {
        $attributes = [
            [
                'attribute' => CreditmemoInterface::AW_CTQ_AMOUNT,
                'type' => 'creditmemo',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CreditmemoInterface::BASE_AW_CTQ_AMOUNT,
                'type' => 'creditmemo',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],

            [
                'attribute' => CreditmemoItemInterface::AW_CTQ_AMOUNT,
                'type' => 'creditmemo_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CreditmemoItemInterface::BASE_AW_CTQ_AMOUNT,
                'type' => 'creditmemo_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],

            [
                'attribute' => InvoiceInterface::AW_CTQ_AMOUNT,
                'type' => 'invoice',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => InvoiceInterface::BASE_AW_CTQ_AMOUNT,
                'type' => 'invoice',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],

            [
                'attribute' => InvoiceItemInterface::AW_CTQ_AMOUNT,
                'type' => 'invoice_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => InvoiceItemInterface::BASE_AW_CTQ_AMOUNT,
                'type' => 'invoice_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],

            [
                'attribute' => OrderInterface::AW_CTQ_AMOUNT,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderInterface::BASE_AW_CTQ_AMOUNT,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderInterface::AW_CTQ_INVOICED,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderInterface::BASE_AW_CTQ_INVOICED,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderInterface::AW_CTQ_REFUNDED,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderInterface::BASE_AW_CTQ_REFUNDED,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],

            [
                'attribute' => OrderItemInterface::AW_CTQ_PERCENT,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderItemInterface::AW_CTQ_AMOUNT,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderItemInterface::BASE_AW_CTQ_AMOUNT,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderItemInterface::AW_CTQ_INVOICED,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderItemInterface::BASE_AW_CTQ_INVOICED,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderItemInterface::AW_CTQ_REFUNDED,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderItemInterface::BASE_AW_CTQ_REFUNDED,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ]
        ];

        return $attributes;
    }

    /**
     * Remove entity attribute. Overwritten for flat entities support
     *
     * @param int|string $entityTypeId
     * @param string $code
     * @return $this
     */
    public function removeAttribute($entityTypeId, $code)
    {
        if (isset($this->_flatEntityTables[$entityTypeId])) {
            if ($this->_flatTableExist($this->_flatEntityTables[$entityTypeId])) {
                $this->removeFlatAttribute($this->_flatEntityTables[$entityTypeId], $code);
            }
            if ($this->_flatTableExist($this->_flatEntityTables[$entityTypeId]) . '_grid') {
                $this->removeGridAttribute($this->_flatEntityTables[$entityTypeId] . '_grid', $code, $entityTypeId);
            }
        } else {
            parent::removeAttribute($entityTypeId, $code);
        }

        return $this;
    }

    /**
     * Remove attribute as separate column in the table
     *
     * @param string $table
     * @param string $attribute
     * @return $this
     */
    protected function removeFlatAttribute($table, $attribute)
    {
        $tableInfo = $this->getConnection()->describeTable($this->getTable($table));
        if (isset($tableInfo[$attribute])) {
            $this->getConnection()->dropColumn($this->getTable($table), $attribute);
        }

        return $this;
    }

    /**
     * Remove attribute from grid table if necessary
     *
     * @param string $table
     * @param string $attribute
     * @param string $entityTypeId
     * @return $this
     */
    protected function removeGridAttribute($table, $attribute, $entityTypeId)
    {
        $tableInfo = $this->getConnection()->describeTable($this->getTable($table));
        if (in_array($entityTypeId, $this->_flatEntitiesGrid) && isset($tableInfo[$attribute])) {
            $this->getConnection()->dropColumn($this->getTable($table), $attribute);
        }

        return $this;
    }
}
