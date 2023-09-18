<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface QuoteSearchResultsInterface
 * @api
 */
interface QuoteSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get quote list
     *
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface[]
     */
    public function getItems();

    /**
     * Set quote list
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
