<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aventi\CustomFilters\Plugin\Magento\Catalog\Model;

/**
 * Catalog view layer model
 *
 * @api
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Layer
{
    /**
     * Retrieve current layer product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function aroundgetProductCollection(\Magento\Catalog\Model\Layer $subject, \Closure $proceed)
    {
        $selectedDirection = 'DESC';
        $collection = $proceed();
        $collection->getSelect()->order('is_salable ' . $selectedDirection);
        return $collection;
    }
}
