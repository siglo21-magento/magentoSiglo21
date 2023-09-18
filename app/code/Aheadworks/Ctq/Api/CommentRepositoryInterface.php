<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api;

/**
 * Interface CommentRepositoryInterface
 * @api
 */
interface CommentRepositoryInterface
{
    /**
     * Save comment
     *
     * @param \Aheadworks\Ctq\Api\Data\CommentInterface $comment
     * @return \Aheadworks\Ctq\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Ctq\Api\Data\CommentInterface $comment);

    /**
     * Retrieve comment by id
     *
     * @param int $commentId
     * @return \Aheadworks\Ctq\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($commentId);

    /**
     * Retrieve comment matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ctq\Api\Data\CommentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
