<?php
namespace Aventi\ExcelModeView\Model\Config\Source;

class ListMode extends \Magento\Catalog\Model\Config\Source\ListMode
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'grid', 'label' => __('Grid Only')],
            ['value' => 'list', 'label' => __('List Only')],
            ['value' => 'grid-list', 'label' => __('Grid (default) / List')],
            ['value' => 'list-grid', 'label' => __('List (default) / Grid')],
            ['value' => 'list-grid-excel', 'label' => __('List (default) / Grid / Excel')],
            ['value' => 'list-excel-grid', 'label' => __('List (default) / Excel / Grid')],
            ['value' => 'excel-list-grid', 'label' => __('Excel (default) / List / Grid')],
            ['value' => 'excel-grid-list', 'label' => __('Excel (default) / Grid / List')]
        ];
    }

}