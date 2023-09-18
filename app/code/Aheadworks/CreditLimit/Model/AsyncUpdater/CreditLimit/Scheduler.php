<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\AsyncUpdater\CreditLimit;

use Aheadworks\CreditLimit\Model\Service\CreditLimitJobService;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class Scheduler
 *
 * @package Aheadworks\CreditLimit\Model\AsyncUpdater\CreditLimit
 */
class Scheduler
{
    /**
     * @var UpdateDataProcessor
     */
    private $updateDataProcessor;

    /**
     * @var Creator
     */
    private $jobCreator;

    /**
     * @var CreditLimitJobService
     */
    private $jobManagement;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @param UpdateDataProcessor $updateDataProcessor
     * @param Creator $jobCreator
     * @param CreditLimitJobService $jobManagement
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        UpdateDataProcessor $updateDataProcessor,
        Creator $jobCreator,
        CreditLimitJobService $jobManagement,
        ManagerInterface $messageManager
    ) {
        $this->updateDataProcessor = $updateDataProcessor;
        $this->jobCreator = $jobCreator;
        $this->jobManagement = $jobManagement;
        $this->messageManager = $messageManager;
    }

    /**
     * Schedule update
     *
     * @param array $value
     * @param int $websiteId
     * @throws \Exception
     */
    public function scheduleUpdate($value, $websiteId)
    {
        $dataToUpdate = $this->updateDataProcessor->prepareDataToUpdate($value, $websiteId);
        foreach ($dataToUpdate as $data) {
            $job = $this->jobCreator->createNewJob($data);
            $this->jobManagement->addNewJob($job);
        }
        if (!empty($dataToUpdate)) {
            $this->messageManager->addNotice('Credit limit is scheduled to be applied.');
        }
    }
}
