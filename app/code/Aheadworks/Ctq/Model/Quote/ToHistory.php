<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote;

use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote;
use Aheadworks\Ctq\Model\Quote\History\LogAction\Builder;
use Aheadworks\Ctq\Model\Source\Owner;
use Aheadworks\Ctq\Model\Source\History\Status;

/**
 * Class ToHistory
 * @package Aheadworks\Ctq\Model\Quote
 */
class ToHistory
{
    /**
     * @var HistoryInterfaceFactory
     */
    private $historyFactory;

    /**
     * @var Builder
     */
    private $historyActionBuilder;

    /**
     * @param HistoryInterfaceFactory $historyFactory
     * @param Builder $historyActionBuilder
     */
    public function __construct(
        HistoryInterfaceFactory $historyFactory,
        Builder $historyActionBuilder
    ) {
        $this->historyFactory = $historyFactory;
        $this->historyActionBuilder = $historyActionBuilder;
    }

    /**
     * Create history log from quote
     *
     * @param QuoteInterface|Quote $quote
     * @return HistoryInterface|null
     */
    public function convert($quote)
    {
        $history = null;
        $quoteHistoryActions = $this->historyActionBuilder->build($quote);
        if ($quoteHistoryActions) {
            /** @var HistoryInterface $history */
            $history = $this->historyFactory->create();
            $isSeller = $quote->getIsSeller();
            $history
                ->setOwnerType($isSeller ? Owner::SELLER : Owner::BUYER)
                ->setOwnerId($isSeller ? $quote->getSellerId() : $quote->getCustomerId());

            if (!$quote->getOrigData(QuoteInterface::ID)) {
                $status = Status::CREATED_QUOTE;
            } else {
                $status = Status::UPDATED_QUOTE;
            }
            $history
                ->setQuoteId($quote->getId())
                ->setStatus($status)
                ->setActions($quoteHistoryActions);
        }

        return $history;
    }
}
