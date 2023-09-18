<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Api;

/**
 * Interface SummaryRepositoryInterface
 * @api
 */
interface SummaryRepositoryInterface
{
    /**
     * Save credit limit summary
     *
     * @param \Aheadworks\CreditLimit\Api\Data\SummaryInterface $creditSummary
     * @return \Aheadworks\CreditLimit\Api\Data\SummaryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\CreditLimit\Api\Data\SummaryInterface $creditSummary);

    /**
     * Retrieve credit limit summary by customer ID
     *
     * @param int $customerId
     * @param bool $reload
     * @return \Aheadworks\CreditLimit\Api\Data\SummaryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCustomerId($customerId, $reload = false);

    /**
     * Retrieve credit limit summary items matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\CreditLimit\Api\Data\SummarySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
