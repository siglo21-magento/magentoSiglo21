<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\Customer\Quote;

use Aheadworks\Ctq\Api\CommentRepositoryInterface;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class CommentList
 * @package Aheadworks\Ctq\ViewModel\Customer\Quote
 */
class CommentList implements ArgumentInterface
{
    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var CommentInterface[]|null
     */
    private $commentList;

    /**
     * @param CommentRepositoryInterface $commentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        CommentRepositoryInterface $commentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->commentRepository = $commentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Retrieve comment list
     *
     * @param int $quoteId
     * @return CommentInterface[]|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCommentList($quoteId)
    {
        if (null === $this->commentList) {
            $sortOrder = $this->sortOrderBuilder
                ->setField(CommentInterface::CREATED_AT)
                ->setDirection(SortOrder::SORT_ASC)
                ->create();

            $this->searchCriteriaBuilder
                ->addFilter(CommentInterface::QUOTE_ID, ['eq' => $quoteId])
                ->addSortOrder($sortOrder);

            $this->commentList = $this->commentRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        }

        return $this->commentList;
    }
}
