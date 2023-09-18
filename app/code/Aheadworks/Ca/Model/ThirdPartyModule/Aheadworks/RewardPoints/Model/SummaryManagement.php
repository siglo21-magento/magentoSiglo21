<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class SummaryManagement
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model
 */
class SummaryManagement
{
    /**
     * @var \Aheadworks\RewardPoints\Api\PointsSummaryRepositoryInterface
     */
    private $pointsSummaryRepository;

    /**
     * @var \Aheadworks\RewardPoints\Model\Calculator\RateCalculator
     */
    private $rateCalculator;

    /**
     * @var \Aheadworks\RewardPoints\Model\Config;
     */
    private $config;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->pointsSummaryRepository =
            $objectManager->get(\Aheadworks\RewardPoints\Api\PointsSummaryRepositoryInterface::class);
        $this->config = $objectManager->get(\Aheadworks\RewardPoints\Model\Config::class);
        $this->rateCalculator = $objectManager->get(\Aheadworks\RewardPoints\Model\Calculator\RateCalculator::class);
    }

    /**
     * Get minimum number of points to use
     *
     * @param int $customerId
     * @param int|null $websiteId
     * @return int
     */
    public function getMinNumberOfPointsToUse($customerId, $websiteId = null)
    {
        try {
            $pointSummary = $this->pointsSummaryRepository->get($customerId);
            $minNumber =  $this->calculateMinNumberOfPointsToUse($pointSummary->getPoints(), $websiteId);
        } catch (\Exception $exception) {
            $minNumber = 0;
        }

        return $minNumber;
    }

    /**
     * Calculate once min balance
     *
     * @param int $balance
     * @param int|null $websiteId
     * @return int
     */
    private function calculateMinNumberOfPointsToUse($balance, $websiteId = null)
    {
        if ($onceMinBalance = $this->config->getOnceMinBalance($websiteId)) {
            $onceMinBalance = max(0, (int)$onceMinBalance);
            if ($balance >= $onceMinBalance) {
                $onceMinBalance = 0;
            }
        }
        return $onceMinBalance;
    }

    /**
     * Get customer points balance
     *
     * @param int $customerId
     * @return int
     */
    public function getCustomerPointsBalance($customerId)
    {
        try {
            $pointSummary = $this->pointsSummaryRepository->get($customerId);
            $balance = (int)$pointSummary->getPoints();
        } catch (\Exception $exception) {
            $balance = 0;
        }

        return $balance;
    }

    /**
     * Get customer points balance in base currency
     *
     * @param CustomerInterface $customer
     * @return int
     * @throws NoSuchEntityException
     */
    public function getCustomerPointsBalanceBaseCurrency($customer)
    {
        $pointSummary = $this->pointsSummaryRepository->get($customer->getId());
        $balanceBaseCurrency = $this->rateCalculator->calculateRewardDiscount(
            $customer->getId(),
            $pointSummary->getPoints(),
            $customer->getWebsiteId()
        );
        return $this->priceCurrency->format(
            $balanceBaseCurrency,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $customer->getStoreId()
        );
    }
}
