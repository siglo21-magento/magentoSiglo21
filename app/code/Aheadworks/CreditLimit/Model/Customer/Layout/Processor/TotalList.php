<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Customer\Layout\Processor;

use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\CreditLimit\Model\Customer\CreditLimit\DataProvider as CreditLimitDataProvider;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class TotalList
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Layout\Processor
 */
class TotalList
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var CreditLimitDataProvider
     */
    private $creditLimitDataProvider;

    /**
     * @param ArrayManager $arrayManager
     * @param CreditLimitDataProvider $creditLimitDataProvider
     */
    public function __construct(
        ArrayManager $arrayManager,
        CreditLimitDataProvider $creditLimitDataProvider
    ) {
        $this->arrayManager = $arrayManager;
        $this->creditLimitDataProvider = $creditLimitDataProvider;
    }

    /**
     * Process js layout of block
     *
     * @param array $jsLayout
     * @param int $customerId
     * @param int $websiteId
     * @return array
     * @throws LocalizedException
     */
    public function process($jsLayout, $customerId, $websiteId)
    {
        $optionsProviderPath = 'components/aw_cl_top_totals_data_provider';
        $jsLayout = $this->arrayManager->merge(
            $optionsProviderPath,
            $jsLayout,
            [
                'data' => $this->creditLimitDataProvider->getData($customerId, $websiteId)
            ]
        );

        return $jsLayout;
    }
}
