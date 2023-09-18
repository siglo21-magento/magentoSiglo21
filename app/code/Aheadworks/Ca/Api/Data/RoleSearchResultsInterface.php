<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface RoleSearchResultsInterface
 * @api
 */
interface RoleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get role list
     *
     * @return \Aheadworks\Ca\Api\Data\RoleInterface[]
     */
    public function getItems();

    /**
     * Set role list
     *
     * @param \Aheadworks\Ca\Api\Data\RoleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
