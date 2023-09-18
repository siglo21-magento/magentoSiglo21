<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

use Magento\Framework\Phrase;
use Magento\Backend\Block\Widget\Button as WidgetButton;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ProductSelection
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit
 */
class ProductSelection extends AbstractEdit
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('aw_ctq_quote_edit_product_selection');
    }

    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Please select products');
    }

    /**
     * Get buttons html
     *
     * @throws LocalizedException
     */
    public function getButtonsHtml()
    {
        $addButtonData = [
            'label' => __('Add Selected Product(s) to Quote'),
            'onclick' => 'quote.productGridAddSelected()',
            'class' => 'action-add action-secondary',
        ];
        return $this->getLayout()
            ->createBlock(WidgetButton::class)
            ->setData($addButtonData)
            ->toHtml();
    }

    /**
     * Get header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }
}
