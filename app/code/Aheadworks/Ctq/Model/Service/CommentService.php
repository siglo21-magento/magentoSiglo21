<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Service;

use Aheadworks\Ctq\Api\CommentManagementInterface;
use Aheadworks\Ctq\Api\Data\CommentAttachmentInterface;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\CommentRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CommentService
 * @package Aheadworks\Ctq\Model\Service
 */
class CommentService implements CommentManagementInterface
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
     * @param CommentRepositoryInterface $commentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CommentRepositoryInterface $commentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->commentRepository = $commentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function addComment(CommentInterface $comment)
    {
        try {
            $comment = $this->commentRepository->save($comment);
        } catch (\Exception $e) {
            throw new LocalizedException(__('Could not post new comment.'), $e);
        }

        return $comment;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachment($fileName, $commentId, $quoteId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(CommentInterface::ID, $commentId)
            ->addFilter(CommentInterface::QUOTE_ID, $quoteId)
            ->addFilter(CommentAttachmentInterface::FILE_NAME, $fileName);

        $comments = $this->commentRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        $comment = array_shift($comments);

        if (empty($comment)) {
            throw new LocalizedException(__('File not found.'));
        }

        foreach ($comment->getAttachments() as $attachment) {
            if ($attachment->getFileName() == $fileName) {
                return $attachment;
            }
        }
        throw new LocalizedException(__('File not found.'));
    }
}
