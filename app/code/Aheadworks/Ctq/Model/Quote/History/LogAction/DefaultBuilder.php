<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\History\LogAction;

use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Api\Data\HistoryActionInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Source\History\Action\Status as ActionStatus;
use Magento\Framework\Api\SimpleDataObjectConverter;

/**
 * Class DefaultBuilder
 * @package Aheadworks\Ctq\Model\Quote\History\LogAction
 */
class DefaultBuilder implements BuilderInterface
{
    /**
     * @var HistoryActionInterfaceFactory
     */
    private $historyActionFactory;

    /**
     * @var null|string
     */
    private $attribute;

    /**
     * @var null|string
     */
    private $actionType;

    /**
     * @var bool
     */
    private $isNewAddToLog;

    /**
     * @param HistoryActionInterfaceFactory $historyActionFactory
     * @param string|null $attribute
     * @param string|null $actionType
     * @param bool $isNewAddToLog
     */
    public function __construct(
        HistoryActionInterfaceFactory $historyActionFactory,
        $attribute = null,
        $actionType = null,
        $isNewAddToLog = false
    ) {
        $this->historyActionFactory = $historyActionFactory;
        $this->attribute = $attribute;
        $this->actionType = $actionType;
        $this->isNewAddToLog = $isNewAddToLog;
    }

    /**
     * {@inheritdoc}
     */
    public function build($quote)
    {
        $historyActions = [];
        $isNewObject = $quote->getOrigData(QuoteInterface::ID) === null;
        $getterName = 'get' . ucfirst(SimpleDataObjectConverter::snakeCaseToCamelCase($this->attribute));
        $oldValue = $quote->getOrigData($this->attribute);
        $newValue = $quote->{$getterName}();

        $isAddToLog = ($this->isNewAddToLog && $isNewObject) || !$isNewObject;
        if ($isAddToLog && $oldValue != $newValue) {
            $actionStatus = $oldValue === null ? ActionStatus::CREATED : ActionStatus::UPDATED;
            /** @var HistoryActionInterface $historyAction */
            $historyAction = $this->historyActionFactory->create();
            $historyAction
                ->setType($this->actionType)
                ->setStatus($actionStatus)
                ->setOldValue($oldValue)
                ->setValue($newValue);
            $historyActions[] = $historyAction;
        }
        return $historyActions;
    }
}
