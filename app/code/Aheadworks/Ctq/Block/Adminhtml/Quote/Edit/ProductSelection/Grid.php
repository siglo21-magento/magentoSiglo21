<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\ProductSelection;

use Magento\Backend\Helper\Data as HelperData;
use Magento\Backend\Block\Template\Context;
use Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Product as ProductRenderer;
use Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Price as PriceRenderer;
use Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Qty as QtyRenderer;
use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Block\Widget\Grid\Column as GridColumn;
use Magento\Sales\Model\Config as SalesConfig;
use Magento\Catalog\Model\Config as CatalogConfig;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as SessionQuote;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

/**
 * Class Grid
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\ProductSelection
 */
class Grid extends ExtendedGrid
{
    /**
     * Sales config
     *
     * @var SalesConfig
     */
    protected $salesConfig;

    /**
     * Session quote
     *
     * @var SessionQuote
     */
    protected $sessionQuote;

    /**
     * Catalog config
     *
     * @var CatalogConfig
     */
    protected $catalogConfig;

    /**
     * Product collection factory
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param HelperData $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param CatalogConfig $catalogConfig
     * @param SessionQuote $sessionQuote
     * @param SalesConfig $salesConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $backendHelper,
        CollectionFactory $collectionFactory,
        CatalogConfig $catalogConfig,
        SessionQuote $sessionQuote,
        SalesConfig $salesConfig,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->catalogConfig = $catalogConfig;
        $this->sessionQuote = $sessionQuote;
        $this->salesConfig = $salesConfig;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('aw_ctq_quote_edit_product_search_grid');
        $this->setRowClickCallback('quote.productGridRowClick.bind(quote)');
        $this->setCheckboxCheckCallback('quote.productGridCheckboxCheck.bind(quote)');
        $this->setRowInitCallback('quote.productGridRowInit.bind(quote)');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Retrieve quote store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->sessionQuote->getStore();
    }

    /**
     * Retrieve quote object
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->sessionQuote->getQuote();
    }

    /**
     * Add column filter to collection
     *
     * @param GridColumn $column
     * @return $this
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection to be displayed in the grid
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $attributes = $this->catalogConfig->getProductAttributes();
        /* @var $collection Collection */
        $collection = $this->collectionFactory->create();
        $collection
            ->setStore($this->getStore())->addAttributeToSelect($attributes)
            ->addAttributeToSelect('sku')
            ->addStoreFilter()
            ->addAttributeToFilter('type_id', $this->salesConfig->getAvailableProductTypes())
            ->addAttributeToSelect('gift_message_available');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'index' => 'entity_id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Product'),
                'renderer' => ProductRenderer::class,
                'index' => 'name'
            ]
        );
        $this->addColumn('sku', ['header' => __('SKU'), 'index' => 'sku']);
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'column_css_class' => 'price',
                'type' => 'currency',
                'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
                'rate' => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
                'index' => 'price',
                'renderer' => PriceRenderer::class
            ]
        );

        $this->addColumn(
            'in_products',
            [
                'header' => __('Select'),
                'type' => 'checkbox',
                'name' => 'in_products',
                'values' => $this->_getSelectedProducts(),
                'index' => 'entity_id',
                'sortable' => false,
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select'
            ]
        );

        $this->addColumn(
            'qty',
            [
                'filter' => false,
                'sortable' => false,
                'header' => __('Quantity'),
                'renderer' => QtyRenderer::class,
                'name' => 'qty',
                'inline_css' => 'qty',
                'type' => 'input',
                'validate_class' => 'validate-number',
                'index' => 'qty'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/updateBlock',
            ['block' => 'search_grid', '_current' => true, 'collapse' => null]
        );
    }

    /**
     * Get selected products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products', []);

        return $products;
    }

    /**
     * Add custom options to product collection
     *
     * @return $this
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->addOptionsToResult();
        return parent::_afterLoadCollection();
    }

    /**
     * @inheritdoc
     */
    public function getRequireJsDependencies()
    {
        return ['Aheadworks_Ctq/js/quote/edit/form'];
    }
}
