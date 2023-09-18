<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface TransactionSearchResultsInterface
 * @api
 */
interface TransactionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of transactions
     *
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionInterface[]
     */
    public function getItems();

    /**
     * Set list of transactions
     *
     * @param \Aheadworks\CreditLimit\Api\Data\TransactionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
