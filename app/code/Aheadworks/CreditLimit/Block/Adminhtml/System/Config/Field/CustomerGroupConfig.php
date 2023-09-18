<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;

/**
 * Class CustomerGroupConfig
 *
 * @package Aheadworks\CreditLimit\Block\Adminhtml\System\Config\Field
 */
class CustomerGroupConfig extends AbstractFieldArray
{
    /**
     * @var CustomerGroupRenderer
     */
    private $customerGroupRenderer;

    /**
     * Prepare rendering the new field by adding all the needed columns
     *
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'customer_group_id',
            [
                'label' => __('Customer Group'),
                'renderer' => $this->getCustomerGroupRenderer()
            ]
        );
        $this->addColumn(
            SummaryInterface::CREDIT_LIMIT,
            [
                'label' => __('Credit Limit'),
                'class' => 'required-entry validate-number validate-zero-or-greater'
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add New');
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $optionHash = $this->getCustomerGroupRenderer()->calcOptionHash($row->getData('customer_group_id'));
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $optionHash] = 'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }

    /**
     * Retrieve customer group column renderer
     *
     * @return CustomerGroupRenderer
     * @throws LocalizedException
     */
    private function getCustomerGroupRenderer()
    {
        if (!$this->customerGroupRenderer) {
                $this->customerGroupRenderer = $this->getLayout()->createBlock(
                    CustomerGroupRenderer::class,
                    '',
                    [
                        'data' => [
                            'is_render_to_js_template' => true,
                            'class' => 'required-entry validate-select',
                            'extra_params' => 'style="width:125px" '
                        ]
                    ]
                );
        }
        return $this->customerGroupRenderer;
    }
}
