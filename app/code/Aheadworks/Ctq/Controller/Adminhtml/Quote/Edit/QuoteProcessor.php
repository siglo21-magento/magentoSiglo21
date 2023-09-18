<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\CommentInterfaceFactory;
use Aheadworks\Ctq\Model\Source\Owner;

/**
 * Class QuoteProcessor
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit
 */
class QuoteProcessor
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var QuoteInterfaceFactory
     */
    private $quoteFactory;

    /**
     * @var CommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param QuoteRepositoryInterface $quoteRepository
     * @param QuoteInterfaceFactory $quoteFactory
     * @param CommentInterfaceFactory $commentFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        QuoteRepositoryInterface $quoteRepository,
        QuoteInterfaceFactory $quoteFactory,
        CommentInterfaceFactory $commentFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->commentFactory = $commentFactory;
    }

    /**
     * Prepare post object
     *
     * @param array $postData
     * @return QuoteInterface
     * @throws LocalizedException
     */
    public function prepare($postData)
    {
        $quote = $this->prepareQuote($postData['quote']);
        if (!empty($postData['comment'])) {
            $comment = $this->prepareComment($quote->getSellerId(), $postData);
            $quote->setComment($comment);
        }

        return $quote;
    }

    /**
     * Prepare quote
     *
     * @param array $data
     * @return QuoteInterface
     * @throws LocalizedException|\Exception
     */
    protected function prepareQuote($data)
    {
        $quoteId = $data['quote_id'] ?? null;
        $quoteObject = $quoteId
            ? $this->quoteRepository->get($quoteId)
            : $this->quoteFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $quoteObject,
            $data,
            QuoteInterface::class
        );

        return $quoteObject;
    }

    /**
     * Add comment
     *
     * @param int $sellerId
     * @param array $postData
     * @return CommentInterface
     */
    private function prepareComment($sellerId, $postData)
    {
        /** @var CommentInterface $commentObject */
        $commentObject = $this->commentFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $commentObject,
            $postData,
            CommentInterface::class
        );
        $commentObject
            ->setOwnerId($sellerId)
            ->setOwnerType(Owner::SELLER);

        return $commentObject;
    }
}
