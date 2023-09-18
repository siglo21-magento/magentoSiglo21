<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface GroupSearchResultsInterface
 * @api
 */
interface GroupSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get group list
     *
     * @return \Aheadworks\Ca\Api\Data\GroupInterface[]
     */
    public function getItems();

    /**
     * Set group list
     *
     * @param \Aheadworks\Ca\Api\Data\GroupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
