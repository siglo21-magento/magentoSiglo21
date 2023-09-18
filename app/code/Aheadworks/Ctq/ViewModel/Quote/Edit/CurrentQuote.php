<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\Quote\Edit;

use Aheadworks\Ctq\Api\SellerActionManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Source\Quote\Action\Type;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ctq\Api\Data\QuoteActionInterface;

/**
 * Class CurrentQuote
 *
 * @package Aheadworks\Ctq\ViewModel\Quote\Edit
 */
class CurrentQuote implements ArgumentInterface
{
    /**
     * @var SellerActionManagementInterface
     */
    private $sellerActionManagement;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var bool
     */
    private $isEditQuote;

    /**
     * @param SellerActionManagementInterface $sellerActionManagement
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        SellerActionManagementInterface $sellerActionManagement,
        QuoteRepositoryInterface $quoteRepository
    ) {
        $this->sellerActionManagement = $sellerActionManagement;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Retrieve available quote actions
     *
     * @param QuoteInterface $quote
     * @return QuoteActionInterface[]
     */
    public function getAvailableQuoteActions($quote)
    {
        return $this->sellerActionManagement->getAvailableQuoteActions($quote);
    }

    /**
     * Check if edit quote or not
     *
     * @param int $quoteId
     * @return bool
     */
    public function isEditQuote($quoteId)
    {
        try {
            $quote = $this->quoteRepository->get($quoteId);
        } catch (NoSuchEntityException $exception) {
            return true;
        }

        if ($this->isEditQuote === null) {
            $this->isEditQuote = false;
            foreach ($this->getAvailableQuoteActions($quote) as $action) {
                if ($action->getType() == Type::EDIT) {
                    $this->isEditQuote = true;
                    break;
                }
            }
        }
        return $this->isEditQuote;
    }
}
