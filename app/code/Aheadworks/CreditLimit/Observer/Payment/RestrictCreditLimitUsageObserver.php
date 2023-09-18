<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Observer\Payment;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Quote\Model\Quote;
use Magento\Payment\Model\Method\AbstractMethod;
use Aheadworks\CreditLimit\Model\Checkout\ConfigProvider;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Model\Payment\AvailabilityChecker;

/**
 * Class RestrictCreditLimitUsageObserver
 *
 * @package Aheadworks\CreditLimit\Observer\Payment
 */
class RestrictCreditLimitUsageObserver implements ObserverInterface
{
    /**
     * @var AvailabilityChecker
     */
    private $availabilityChecker;

    /**
     * @param AvailabilityChecker $availabilityChecker
     */
    public function __construct(
        AvailabilityChecker $availabilityChecker
    ) {
        $this->availabilityChecker = $availabilityChecker;
    }

    /**
     * Restrict credit limit payment usage
     *
     * @param EventObserver $observer
     * @throws LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        $event = $observer->getEvent();
        /** @var AbstractMethod $methodInstance */
        $methodInstance = $event->getMethodInstance();
        /** @var Quote $quote */
        $quote = $event->getQuote();

        if ($methodInstance->getCode() == ConfigProvider::METHOD_CODE
            && !$this->isCreditLimitAvailable($quote)
        ) {
            /** @var DataObject $result */
            $result = $observer->getEvent()->getResult();
            $result->setData('is_available', false);
        }
    }

    /**
     * Check if credit limit is available
     *
     * @param Quote|null $quote
     * @return bool
     */
    private function isCreditLimitAvailable($quote)
    {
        return $quote->getIsSuperMode()
            ? $this->availabilityChecker->isAvailableInAdmin($quote)
            : $this->availabilityChecker->isAvailableOnFrontend($quote);
    }
}
