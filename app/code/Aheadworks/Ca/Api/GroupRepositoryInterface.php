<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api;

/**
 * Interface GroupRepositoryInterface
 * @api
 */
interface GroupRepositoryInterface
{
    /**
     * Save group
     *
     * @param \Aheadworks\Ca\Api\Data\GroupInterface $group
     * @return \Aheadworks\Ca\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Ca\Api\Data\GroupInterface $group);

    /**
     * Retrieve group by id
     *
     * @param int $groupId
     * @return \Aheadworks\Ca\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($groupId);

    /**
     * Retrieve group list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ca\Api\Data\GroupSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
