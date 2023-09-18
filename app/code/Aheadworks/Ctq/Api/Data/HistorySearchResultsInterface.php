<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface HistorySearchResultsInterface
 * @api
 */
interface HistorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get history list
     *
     * @return \Aheadworks\Ctq\Api\Data\HistoryInterface[]
     */
    public function getItems();

    /**
     * Set history list
     *
     * @param \Aheadworks\Ctq\Api\Data\HistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
