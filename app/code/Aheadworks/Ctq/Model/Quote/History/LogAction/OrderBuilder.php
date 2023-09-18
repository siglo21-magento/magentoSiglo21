<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\History\LogAction;

use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Api\Data\HistoryActionInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Order\DataProvider as OrderDataProvider;
use Aheadworks\Ctq\Model\Source\History\Action\Status as ActionStatus;
use Aheadworks\Ctq\Model\Source\History\Action\Type;

/**
 * Class OrderBuilder
 * @package Aheadworks\Ctq\Model\Quote\History\LogAction
 */
class OrderBuilder implements BuilderInterface
{
    /**
     * @var HistoryActionInterfaceFactory
     */
    private $historyActionFactory;

    /**
     * @var OrderDataProvider
     */
    private $orderDataProvider;

    /**
     * @param HistoryActionInterfaceFactory $historyActionFactory
     * @param OrderDataProvider $orderDataProvider
     */
    public function __construct(
        HistoryActionInterfaceFactory $historyActionFactory,
        OrderDataProvider $orderDataProvider
    ) {
        $this->historyActionFactory = $historyActionFactory;
        $this->orderDataProvider = $orderDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function build($quote)
    {
        $historyActions = [];
        $isNewObject = $quote->getOrigData(QuoteInterface::ID) === null;
        $oldValue = $quote->getOrigData(QuoteInterface::ORDER_ID);
        $newValue = $quote->getOrderId();

        if (!$isNewObject && !$oldValue && $newValue) {
            $orderIncrementId = $this->orderDataProvider->getOrderIncrementId($newValue);
            if ($orderIncrementId) {
                $valueFormatted = __('Quote ordered. Order #%1', $orderIncrementId);
                /** @var HistoryActionInterface $historyAction */
                $historyAction = $this->historyActionFactory->create();
                $historyAction
                    ->setType(Type::ORDER)
                    ->setStatus(ActionStatus::CREATED)
                    ->setOldValue(null)
                    ->setValue($valueFormatted);
                $historyActions[] = $historyAction;
            }
        }

        return $historyActions;
    }
}
