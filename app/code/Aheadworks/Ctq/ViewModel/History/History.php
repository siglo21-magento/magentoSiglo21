<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\History;

use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Model\Source\History\Status;
use Aheadworks\Ctq\Model\Source\History\Action\Status as ActionStatus;
use Aheadworks\Ctq\Model\Source\History\Action\Type as ActionType;
use Aheadworks\Ctq\Model\Source\Owner;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class History
 * @package Aheadworks\Ctq\ViewModel\History
 */
class History implements ArgumentInterface
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var ActionStatus
     */
    private $actionStatusSource;

    /**
     * @var ActionType
     */
    private $actionTypeSource;

    /**
     * @var Status
     */
    private $statusSource;

    /**
     * @param TimezoneInterface $localeDate
     * @param ActionStatus $actionStatusSource
     * @param ActionType $actionTypeSource
     * @param Status $statusSource
     */
    public function __construct(
        TimezoneInterface $localeDate,
        ActionStatus $actionStatusSource,
        ActionType $actionTypeSource,
        Status $statusSource
    ) {
        $this->localeDate = $localeDate;
        $this->actionStatusSource = $actionStatusSource;
        $this->actionTypeSource = $actionTypeSource;
        $this->statusSource = $statusSource;
    }

    /**
     * Retrieve formatted created at date
     *
     * @param string $createdAt
     * @return string
     */
    public function getCreatedAtFormatted($createdAt)
    {
        return $this->localeDate->formatDateTime($createdAt, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
    }

    /**
     * Get status label
     *
     * @param string $status
     * @return string
     */
    public function getStatusFormatted($status)
    {
        return $this->statusSource->getOptionByCode($status);
    }

    /**
     * Get action status label
     *
     * @param string $status
     * @return string
     */
    public function getActionStatusFormatted($status)
    {
        $statusOptions = $this->actionStatusSource->getOptions();
        return $statusOptions[$status];
    }

    /**
     * Get action type label
     *
     * @param string $type
     * @return string
     */
    public function getActionTypeFormatted($type)
    {
        $typeOptions = $this->actionTypeSource->getOptions();
        return $typeOptions[$type];
    }

    /**
     * Retrieve owner name
     *
     * @param HistoryInterface $history
     * @return string
     */
    public function getOwnerName($history)
    {
        return $history->getOwnerName();
    }
}
