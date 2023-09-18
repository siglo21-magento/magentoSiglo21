<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Source\History\Action;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Aheadworks\Ctq\Model\Source\History\Action
 */
class Type implements OptionSourceInterface
{
    /**#@+
     * Constants defined for history action status
     */
    const QUOTE_ATTRIBUTE = 'quote_attribute';
    const QUOTE_ATTRIBUTE_STATUS = 'quote_attribute_status';
    const QUOTE_ATTRIBUTE_EXPIRATION_DATE = 'quote_attribute_expiration_date';
    const QUOTE_ATTRIBUTE_REMINDER_DATE = 'quote_attribute_reminder_date';
    const QUOTE_ATTRIBUTE_BASE_TOTAL_NEGOTIATED = 'quote_attribute_base_total_negotiated';
    const QUOTE_ATTRIBUTE_BASE_TOTAL = 'quote_attribute_base_total';
    const PRODUCT = 'product';
    const PRODUCT_ATTRIBUTE = 'product_attribute';
    const PRODUCT_ATTRIBUTE_QTY = 'product_attribute_qty';
    const PRODUCT_OPTION = 'product_option';
    const SHIPPING_ADDRESS = 'shipping_address';
    const SHIPPING_ADDRESS_ATTRIBUTE = 'shipping_address_attribute';
    const SHIPPING_METHOD = 'shipping_method';
    const COMMENT = 'comment';
    const COMMENT_ATTACHMENT = 'comment_attachment';
    const CHANGE_ADMIN = 'admin';
    const ORDER = 'order';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::QUOTE_ATTRIBUTE, 'label' => __('Quote Attribute')],
            ['value' => self::QUOTE_ATTRIBUTE_STATUS, 'label' => __('Quote Status')],
            ['value' => self::QUOTE_ATTRIBUTE_EXPIRATION_DATE, 'label' => __('Quote Expiration Date')],
            ['value' => self::QUOTE_ATTRIBUTE_REMINDER_DATE, 'label' => __('Quote Reminder Date')],
            ['value' => self::QUOTE_ATTRIBUTE_BASE_TOTAL_NEGOTIATED, 'label' => __('Quote Total Negotiated')],
            ['value' => self::QUOTE_ATTRIBUTE_BASE_TOTAL, 'label' => __('Quote Total')],

            ['value' => self::PRODUCT, 'label' => __('Product')],
            ['value' => self::PRODUCT_ATTRIBUTE, 'label' => __('Product Attribute')],
            ['value' => self::PRODUCT_ATTRIBUTE_QTY, 'label' => __('Product Qty')],
            ['value' => self::PRODUCT_OPTION, 'label' => __('Product Option')],

            ['value' => self::SHIPPING_ADDRESS, 'label' => __('Shipping Address')],
            ['value' => self::SHIPPING_ADDRESS_ATTRIBUTE, 'label' => __('Shipping Address Attribute')],
            ['value' => self::SHIPPING_METHOD, 'label' => __('Shipping Method')],

            ['value' => self::COMMENT, 'label' => __('Comment')],
            ['value' => self::COMMENT_ATTACHMENT, 'label' => __('Comment Attachment')],

            ['value' => self::CHANGE_ADMIN, 'label' => __('Admin Quote')],

            ['value' => self::ORDER, 'label' => __('Order')]
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        foreach ($this->toOptionArray() as $optionItem) {
            $options[$optionItem['value']] = $optionItem['label'];
        }
        return $options;
    }
}
