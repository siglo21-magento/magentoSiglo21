<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Service;

use Aheadworks\Ctq\Api\SellerPermissionManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsPool;
use Aheadworks\Ctq\Model\Source\Quote\Status;

/**
 * Class SellerPermissionService
 * @package Aheadworks\Ctq\Model\Service
 */
class SellerPermissionService implements SellerPermissionManagementInterface
{
    /**
     * @var RestrictionsPool
     */
    private $statusRestrictionsPool;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param RestrictionsPool $statusRestrictionsPool
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        RestrictionsPool $statusRestrictionsPool,
        QuoteRepositoryInterface $quoteRepository
    ) {
        $this->statusRestrictionsPool = $statusRestrictionsPool;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function canBuyQuote($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $statusRestrictions = $this->statusRestrictionsPool->getRestrictions($quote->getStatus());

        return in_array(Status::ORDERED, $statusRestrictions->getNextAvailableStatuses());
    }
}
