<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface CompanySearchResultsInterface
 * @api
 */
interface CompanySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get comapny list
     *
     * @return \Aheadworks\Ca\Api\Data\CompanyInterface[]
     */
    public function getItems();

    /**
     * Set comapny list
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
