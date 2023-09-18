<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface SummarySearchResultsInterface
 * @api
 */
interface SummarySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get credit limit summary items
     *
     * @return \Aheadworks\CreditLimit\Api\Data\SummaryInterface[]
     */
    public function getItems();

    /**
     * Set credit limit summary items
     *
     * @param \Aheadworks\CreditLimit\Api\Data\SummaryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
