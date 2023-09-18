<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api;

/**
 * Interface ExternalQuoteManagementInterface
 * @api
 */
interface ExternalQuoteManagementInterface
{
    /**
     * Retrieve quote by ID
     *
     * @param int $quoteId
     * @return \Aheadworks\Ctq\Api\Data\ExternalQuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($quoteId);

    /**
     * Retrieve quotes matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ctq\Api\Data\ExternalQuoteInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Make a quote duplication
     *
     * @param int $quoteId
     * @return \Aheadworks\Ctq\Api\Data\ExternalQuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function copyQuote($quoteId);
}
