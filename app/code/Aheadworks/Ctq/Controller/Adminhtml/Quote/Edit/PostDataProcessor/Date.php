<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor;

use Magento\Framework\Stdlib\DateTime\Filter\Date as DateFilter;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Date
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor
 */
class Date implements ProcessorInterface
{
    /**
     * @var DateFilter
     */
    private $dateFilter;

    /**
     * @var StdlibDateTime
     */
    private $dateTime;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @param DateFilter $dateFilter
     * @param StdlibDateTime $dateTime
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        DateFilter $dateFilter,
        StdlibDateTime $dateTime,
        TimezoneInterface $localeDate
    ) {
        $this->dateFilter = $dateFilter;
        $this->dateTime = $dateTime;
        $this->localeDate = $localeDate;
    }

    /**
     * Prepare dates for save
     *
     * @param array $data
     * @return array
     */
    public function process($data)
    {
        if (isset($data['quote']) && is_array($data['quote'])) {
            $data['quote'] = $this->processQuoteDates($data['quote']);
        }

        return $data;
    }

    /**
     * Process quote dates
     *
     * @param array $data
     * @return array
     */
    private function processQuoteDates($data)
    {
        $filterValues = [];
        if (empty($data[QuoteInterface::EXPIRATION_DATE])) {
            $data[QuoteInterface::EXPIRATION_DATE] = null;
        } else {
            $filterValues[QuoteInterface::EXPIRATION_DATE] = $this->dateFilter;
        }

        if (empty($data[QuoteInterface::REMINDER_DATE])) {
            $data[QuoteInterface::REMINDER_DATE] = null;
        } else {
            $filterValues[QuoteInterface::REMINDER_DATE] = $this->dateFilter;
        }

        $inputFilter = new \Zend_Filter_Input(
            $filterValues,
            [],
            $data
        );
        $data = $inputFilter->getUnescaped();

        if ($data[QuoteInterface::REMINDER_DATE]) {
            $data[QuoteInterface::REMINDER_DATE] = $this->prepareDate($data[QuoteInterface::REMINDER_DATE]);
        }
        if ($data[QuoteInterface::EXPIRATION_DATE]) {
            $data[QuoteInterface::EXPIRATION_DATE] = $this->prepareDate($data[QuoteInterface::EXPIRATION_DATE]);
        }

        return $data;
    }

    /**
     * Prepare date
     *
     * @param string $date
     * @return string
     */
    private function prepareDate($date)
    {
        $newDate = new \DateTime(
            $date,
            new \DateTimeZone($this->localeDate->getConfigTimezone())
        );
        $newDate->setTimezone(new \DateTimeZone('UTC'));
        $newDate->setTime(23, 59, 59);

        return $this->dateTime->formatDate($newDate);
    }
}
