<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Service;

use Aheadworks\CreditLimit\Model\ResourceModel\CustomerGroupConfig;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CustomerGroupConfigService
 *
 * @package Aheadworks\CreditLimit\Model\Service
 */
class CustomerGroupService
{
    /**
     * @var CustomerGroupConfig
     */
    private $customerGroupConfigResource;

    /**
     * @param CustomerGroupConfig $customerGroupConfigResource
     */
    public function __construct(
        CustomerGroupConfig $customerGroupConfigResource
    ) {
        $this->customerGroupConfigResource = $customerGroupConfigResource;
    }

    /**
     * Get credit limit for customer group
     *
     * @param int $groupId
     * @param int $websiteId
     * @return float|null
     * @throws LocalizedException
     */
    public function getCreditLimit($groupId, $websiteId)
    {
        $creditLimitDefaultValue = null;
        $customerGroupsConfig = $this->customerGroupConfigResource->loadConfigValue($websiteId);
        foreach ($customerGroupsConfig as $customerGroupConfig) {
            if ($customerGroupConfig['customer_group_id'] == $groupId) {
                $creditLimit = $customerGroupConfig[SummaryInterface::CREDIT_LIMIT];
                $creditLimitDefaultValue = abs($creditLimit);
                break;
            }
        }

        return $creditLimitDefaultValue;
    }

    /**
     * Get credit limit values for website
     *
     * @param int $websiteId
     * @return array
     * @throws LocalizedException
     */
    public function getCreditLimitValuesForWebsite($websiteId)
    {
        $data = $this->customerGroupConfigResource->loadData($websiteId);
        $creditLimitValues = [];
        foreach ($data as $dataRow) {
            $creditLimitValues[$dataRow['customer_group_id']] = abs($dataRow[SummaryInterface::CREDIT_LIMIT]);
        }

        return $creditLimitValues;
    }
}
