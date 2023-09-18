<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\ViewModel;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Class ListViewModelInterface
 * @package Aheadworks\Ca\ViewModel
 */
interface ListViewModelInterface
{
    /**
     * Retrieves search criteria builder
     *
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder();

    /**
     * Retrieve search results
     *
     * @return SearchResultsInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSearchResults();
}
