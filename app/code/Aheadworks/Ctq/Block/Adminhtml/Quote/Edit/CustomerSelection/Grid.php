<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\CustomerSelection;

use Magento\Backend\Block\Widget\Grid\Extended as WidgetGrid;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as HelperData;
use Magento\Sales\Model\ResourceModel\Order\Customer\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Customer\Collection;

/**
 * Class Grid
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\CustomerSelection
 */
class Grid extends WidgetGrid
{
    /**
     * @var CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @param Context $context
     * @param HelperData $backendHelper
     * @param CollectionFactory $customerCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $backendHelper,
        CollectionFactory $customerCollectionFactory,
        array $data = []
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
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
        $this->setId('aw_ctq_quote_customer_grid');
        $this->setUseAjax(true);
        $this->setPagerVisibility(true);
        $this->setDefaultSort('entity_id');
        $this->setRowClickCallback('quote.selectCustomer.bind(quote)');
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Prepare collection to be displayed in the grid
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->customerCollectionFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
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
            ['block' => 'customer_grid']
        );
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
                'header' => __('Name'),
                'index' => 'name'
            ]
        );
        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'index' => 'email'
            ]
        );
        $this->addColumn(
            'billing_telephone',
            [
                'header' => __('Phone'),
                'header_css_class' => 'col-phone',
                'column_css_class' => 'col-phone',
                'index' => 'billing_telephone'
            ]
        );
        $this->addColumn(
            'billing_postcode',
            [
                'header' => __('ZIP/Post Code'),
                'index' => 'billing_postcode'
            ]
        );
        $this->addColumn(
            'billing_country_id',
            [
                'header' => __('Country'),
                'index' => 'billing_country_id',
                'type' => 'country'
            ]
        );
        $this->addColumn(
            'billing_regione',
            [
                'header' => __('State/Province'),
                'index' => 'billing_regione'
            ]
        );
        $this->addColumn(
            'store_name',
            [
                'header' => __('Signed-up Point'),
                'index' => 'store_name',
                'align' => 'center'
            ]
        );
        $this->addColumn(
            'website_name',
            [
                'header' => __('Website'),
                'index' => 'website_name',
                'align' => 'center'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @inheritdoc
     */
    public function getRowUrl($item)
    {
        return $item->getId();
    }

    /**
     * @inheritdoc
     */
    public function getRequireJsDependencies()
    {
        return ['Aheadworks_Ctq/js/quote/edit/form'];
    }
}
