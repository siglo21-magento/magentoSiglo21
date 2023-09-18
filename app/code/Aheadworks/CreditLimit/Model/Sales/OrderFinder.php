<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Sales;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class OrderFinder
 *
 * @package Aheadworks\CreditLimit\Model\Sales
 */
class OrderFinder
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Find order by its increment ID
     *
     * @param int $orderIncrementId
     * @return OrderInterface
     */
    public function findOrderByIncrementId($orderIncrementId)
    {
        $this->searchCriteriaBuilder->addFilter(
            OrderInterface::INCREMENT_ID,
            $orderIncrementId
        );
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $result = $this->orderRepository->getList($searchCriteria);
        $items = $result->getItems();

        return reset($items);
    }
}
