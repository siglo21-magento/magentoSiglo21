<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api;

/**
 * Interface HistoryRepositoryInterface
 * @api
 */
interface HistoryRepositoryInterface
{
    /**
     * Save history
     *
     * @param \Aheadworks\Ctq\Api\Data\HistoryInterface $history
     * @return \Aheadworks\Ctq\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Ctq\Api\Data\HistoryInterface $history);

    /**
     * Retrieve history by id
     *
     * @param int $historyId
     * @return \Aheadworks\Ctq\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($historyId);

    /**
     * Retrieve history matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ctq\Api\Data\HistorySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
