<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Service;

use Aheadworks\Ctq\Api\BuyerActionManagementInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Quote\Action\ActionManagement;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsPool;

/**
 * Class BuyerActionService
 * @package Aheadworks\Ctq\Model\Service
 */
class BuyerActionService implements BuyerActionManagementInterface
{
    /**
     * @var RestrictionsPool
     */
    private $statusRestrictionsPool;

    /**
     * @var ActionManagement
     */
    private $actionManagement;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param RestrictionsPool $statusRestrictionsPool
     * @param ActionManagement $actionManagement
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        RestrictionsPool $statusRestrictionsPool,
        ActionManagement $actionManagement,
        QuoteRepositoryInterface $quoteRepository
    ) {
        $this->statusRestrictionsPool = $statusRestrictionsPool;
        $this->actionManagement = $actionManagement;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableQuoteActions($quote)
    {
        $quote = $quote instanceof QuoteInterface ? $quote : $this->quoteRepository->get($quote);
        $statusRestrictions = $this->statusRestrictionsPool->getRestrictions($quote->getStatus());

        return $this->actionManagement->getActionObjects($statusRestrictions->getBuyerAvailableActions());
    }
}
