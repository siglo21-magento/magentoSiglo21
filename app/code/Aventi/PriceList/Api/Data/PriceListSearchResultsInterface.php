<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceList\Api\Data;

interface PriceListSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get PriceList list.
     * @return \Aventi\PriceList\Api\Data\PriceListInterface[]
     */
    public function getItems();

    /**
     * Set sku list.
     * @param \Aventi\PriceList\Api\Data\PriceListInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

