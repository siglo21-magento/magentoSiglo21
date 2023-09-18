<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\AsyncUpdater\CreditLimit;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\Service\CustomerGroupService;

/**
 * Class UpdateDataProcessor
 *
 * @package Aheadworks\CreditLimit\Model\AsyncUpdater\CreditLimit
 */
class UpdateDataProcessor
{
    /**
     * @var CustomerGroupService
     */
    private $customerGroupService;

    /**
     * @var array
     */
    private $dataToUpdate = [];

    /**
     * @param CustomerGroupService $customerGroupService
     */
    public function __construct(
        CustomerGroupService $customerGroupService
    ) {
        $this->customerGroupService = $customerGroupService;
    }

    /**
     * Prepare data to update
     *
     * @param array $newValueData
     * @param int $websiteId
     * @return array
     * @throws LocalizedException
     */
    public function prepareDataToUpdate($newValueData, $websiteId)
    {
        $this->dataToUpdate = [];
        $oldCreditLimitValues = $this->customerGroupService->getCreditLimitValuesForWebsite($websiteId);
        $newCreditLimitValues = $this->prepareNewCreditLimitValues($newValueData);

        foreach ($newCreditLimitValues as $customerGroup => $creditLimit) {
            if (isset($oldCreditLimitValues[$customerGroup])) {
                if ($creditLimit != $oldCreditLimitValues[$customerGroup]) {
                    $this->addToDataToUpdate($customerGroup, $creditLimit, $websiteId);
                }
                unset($oldCreditLimitValues[$customerGroup]);
            } else {
                $this->addToDataToUpdate($customerGroup, $creditLimit, $websiteId);
            }
        }

        foreach ($oldCreditLimitValues as $customerGroup => $creditLimit) {
            $this->addToDataToUpdate($customerGroup, 0, $websiteId);
        }

        return $this->dataToUpdate;
    }

    /**
     * Prepare new credit limit values
     *
     * @param array $newValueData
     * @return array
     */
    private function prepareNewCreditLimitValues($newValueData)
    {
        $newCreditLimitValues = [];
        foreach ($newValueData as $newData) {
            $newCreditLimitValues[$newData['customer_group_id']] = $newData[SummaryInterface::CREDIT_LIMIT];
        }

        return $newCreditLimitValues;
    }

    /**
     * Prepare credit limit data to update
     *
     * @param int $customerGroup
     * @param float $creditLimit
     * @param int $websiteId
     */
    private function addToDataToUpdate($customerGroup, $creditLimit, $websiteId)
    {
        $this->dataToUpdate[] = [
            'customer_group_id' => $customerGroup,
            SummaryInterface::CREDIT_LIMIT => $creditLimit,
            SummaryInterface::WEBSITE_ID => $websiteId
        ];
    }
}
