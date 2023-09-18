<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Cron;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Aheadworks\CreditLimit\Model\Flag;
use Aheadworks\CreditLimit\Model\FlagFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Management
 *
 * @package Aheadworks\CreditLimit\Cron
 */
class Management
{
    /**
     * Cron run interval in seconds
     */
    const RUN_INTERVAL = 600;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Flag
     */
    private $flag;

    /**
     * @param LoggerInterface $logger
     * @param DateTime $dateTime
     * @param FlagFactory $flagFactory
     */
    public function __construct(
        LoggerInterface $logger,
        DateTime $dateTime,
        FlagFactory $flagFactory
    ) {
        $this->logger = $logger;
        $this->dateTime = $dateTime;
        $this->flag = $flagFactory->create();
    }

    /**
     * Is cron job locked
     *
     * @param string $flag
     * @param int $interval
     * @return bool
     */
    public function isLocked($flag, $interval = self::RUN_INTERVAL)
    {
        $now = $this->getCurrentTime();
        $lastExecTime = (int)$this->getFlagData($flag);
        return $now < $lastExecTime + $interval;
    }

    /**
     * Set flag data
     *
     * @param string $param
     * @return $this
     */
    public function setFlagData($param)
    {
        try {
            $this->flag
                ->unsetData()
                ->setAwClFlag($param)
                ->loadSelf()
                ->setFlagData($this->getCurrentTime())
                ->save();
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return $this;
    }

    /**
     * Get current time
     *
     * @return int
     */
    private function getCurrentTime()
    {
        return $this->dateTime->timestamp();
    }

    /**
     * Get flag data
     *
     * @param string $param
     * @return mixed
     */
    private function getFlagData($param)
    {
        try {
            $this->flag
                ->unsetData()
                ->setAwClFlag($param)
                ->loadSelf();
        } catch (LocalizedException $e) {
            return 0;
        }

        return $this->flag->getFlagData();
    }
}
