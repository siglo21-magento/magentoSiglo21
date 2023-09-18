<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Setup;

use Aheadworks\Ctq\Api\Data\CartInterface;
use Aheadworks\Ctq\Api\Data\CartItemInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Class UpgradeData
 *
 * @package Aheadworks\Ctq\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @param QuoteSetupFactory $quoteSetupFactory
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->quoteSetupFactory = $quoteSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->setupQuoteItemAttribute($setup);
            $this->setupQuoteAttribute($setup);
        }
        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $this->setupQuoteItemSortOrder($setup);
        }
        $setup->endSetup();
    }

    /**
     * Setup quote item attribute
     *
     * @param ModuleDataSetupInterface $setup
     */
    private function setupQuoteItemAttribute(ModuleDataSetupInterface $setup)
    {
        /** @var QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $quoteSetup->addAttribute(
            'quote_item',
            CartItemInterface::AW_CTQ_CALCULATE_TYPE,
            ['type' => Table::TYPE_INTEGER]
        );
    }

    /**
     * Setup quote attribute
     *
     * @param ModuleDataSetupInterface $setup
     */
    private function setupQuoteAttribute(ModuleDataSetupInterface $setup)
    {
        /** @var QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $quoteSetup->addAttribute(
            'quote',
            CartInterface::AW_CTQ_QUOTE_LIST_CUSTOMER_ID,
            ['type' => Table::TYPE_INTEGER]
        );
    }

    /**
     * Setup quote item sort order
     *
     * @param ModuleDataSetupInterface $setup
     */
    private function setupQuoteItemSortOrder(ModuleDataSetupInterface $setup)
    {
        /** @var QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $quoteSetup->addAttribute(
            'quote_item',
            CartItemInterface::AW_CTQ_SORT_ORDER,
            ['type' => Table::TYPE_INTEGER]
        );
    }
}
