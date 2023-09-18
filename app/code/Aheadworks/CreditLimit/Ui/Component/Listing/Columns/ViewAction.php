<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ViewAction
 *
 * @package Aheadworks\CreditLimit\Ui\Component\Listing\Columns
 */
class ViewAction extends Column
{
    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $entityFieldName = $this->getData('config/entityFieldName') ?? 'id';
                if (isset($item[$entityFieldName])) {
                    $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
                    $urlEntityParamName = $this->getData('config/urlEntityParamName') ?? 'id';
                    $params = [
                        $urlEntityParamName => $item[$entityFieldName]
                    ];
                    $additionalParam = $this->getData('config/additionalParamName');
                    if ($additionalParam) {
                        $params[$additionalParam] = $this->getData('config/additionalParamValue');
                    }

                    $item[$this->getData('name')] = [
                        'view' => [
                            'href' => $this->context->getUrl($viewUrlPath, $params),
                            'label' => __('Edit')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
