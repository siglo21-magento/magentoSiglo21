<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class LinkColumn
 *
 * @package Aheadworks\CreditLimit\Ui\Component\Listing\Columns
 */
class LinkColumn extends Column
{
    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
        $urlEntityParamName = $this->getData('config/urlEntityParamName') ?? 'id';
        $entityFieldName = $this->getData('config/entityFieldName') ?? 'id';
        foreach ($dataSource['data']['items'] as &$item) {
            $item[$fieldName . '_label'] = $item[$fieldName];
            $item[$fieldName . '_url'] = $this->context->getUrl(
                $viewUrlPath,
                [$urlEntityParamName => $item[$entityFieldName]]
            );
        }

        return $dataSource;
    }
}
