<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Setup;

use Magento\Quote\Setup\QuoteSetup as MagentoQuoteSetup;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\Ctq\Api\Data\CartInterface;
use Aheadworks\Ctq\Api\Data\CartItemInterface;

/**
 * Class QuoteSetup
 *
 * @package Aheadworks\Ctq\Setup
 */
class QuoteSetup extends MagentoQuoteSetup
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
                'attribute' => CartInterface::AW_CTQ_AMOUNT,
                'type' => 'quote',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CartInterface::BASE_AW_CTQ_AMOUNT,
                'type' => 'quote',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CartInterface::AW_CTQ_SELLER_ID,
                'type' => 'quote',
                'params' => ['type' => Table::TYPE_INTEGER]
            ],
            [
                'attribute' => CartInterface::AW_CTQ_QUOTE_LIST_CUSTOMER_ID,
                'type' => 'quote',
                'params' => ['type' => Table::TYPE_INTEGER]
            ],
            [
                'attribute' => CartItemInterface::AW_CTQ_AMOUNT,
                'type' => 'quote_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CartItemInterface::BASE_AW_CTQ_AMOUNT,
                'type' => 'quote_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CartItemInterface::AW_CTQ_PERCENT,
                'type' => 'quote_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CartItemInterface::AW_CTQ_CALCULATE_TYPE,
                'type' => 'quote_item',
                'params' => ['type' => Table::TYPE_INTEGER]
            ],
            [
                'attribute' => CartInterface::AW_CTQ_AMOUNT,
                'type' => 'quote_address',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CartInterface::BASE_AW_CTQ_AMOUNT,
                'type' => 'quote_address',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CartItemInterface::AW_CTQ_AMOUNT,
                'type' => 'quote_address_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CartItemInterface::BASE_AW_CTQ_AMOUNT,
                'type' => 'quote_address_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CartItemInterface::AW_CTQ_PERCENT,
                'type' => 'quote_address_item',
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
        if (isset($this->_flatEntityTables[$entityTypeId])
            && $this->_flatTableExist($this->_flatEntityTables[$entityTypeId])
        ) {
            $this->removeFlatAttribute($this->_flatEntityTables[$entityTypeId], $code);
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
}
