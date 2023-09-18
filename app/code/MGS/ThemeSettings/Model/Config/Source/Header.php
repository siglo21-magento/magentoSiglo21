<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace MGS\ThemeSettings\Model\Config\Source;

class Header implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
			['value' => 'header1', 'label' => __('Header 1')], 
			['value' => 'header2', 'label' => __('Header 2')], 
			['value' => 'header3', 'label' => __('Header 3')],
			['value' => 'header4', 'label' => __('Header 4')],
			['value' => 'header5', 'label' => __('Header 5')],
			['value' => 'header6', 'label' => __('Header 6')],
			['value' => 'header7', 'label' => __('Header 7')],
			['value' => 'header8', 'label' => __('Header 8')], 
			['value' => 'header9', 'label' => __('Header 9')], 
			['value' => 'header10', 'label' => __('Header 10')], 
			['value' => 'header11', 'label' => __('Header 11')]
		];
    }
}
