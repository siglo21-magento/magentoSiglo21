<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Customer\CreditLimit;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerRegistry;
use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Aheadworks\CreditLimit\Model\Service\CustomerGroupService;

/**
 * Class UpdateManagement
 *
 * @package Aheadworks\CreditLimit\Model\Customer\CreditLimit
 */
class UpdateManagement
{
    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var CreditLimitManagementInterface
     */
    private $creditLimitService;

    /**
     * @var CustomerGroupService
     */
    private $customerGroupService;

    /**
     * @var CustomerManagementInterface
     */
    private $customerService;

    /**
     * @param CustomerRegistry $customerRegistry
     * @param CreditLimitManagementInterface $creditLimitService
     * @param CustomerManagementInterface $customerService
     * @param CustomerGroupService $customerGroupService
     */
    public function __construct(
        CustomerRegistry $customerRegistry,
        CreditLimitManagementInterface $creditLimitService,
        CustomerManagementInterface $customerService,
        CustomerGroupService $customerGroupService
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->creditLimitService = $creditLimitService;
        $this->customerService = $customerService;
        $this->customerGroupService = $customerGroupService;
    }

    /**
     * Create transaction about credit limit update
     *
     * @param int $oldCustomerGroup
     * @param CustomerInterface $customer
     * @param bool $flushCustomerCache
     * @return bool
     * @throws LocalizedException
     */
    public function updateCreditLimitOnGroupChange($oldCustomerGroup, $customer, $flushCustomerCache = false)
    {
        if (!$this->customerService->isCreditLimitAvailable($customer->getId())) {
            return false;
        }
        if (!$this->customerService->isCreditLimitCustom($customer->getId())
            && !$this->isCreditLimitTheSame($oldCustomerGroup, $customer)
        ) {
            if ($flushCustomerCache) {
                $this->customerRegistry->remove($customer->getId());
            }
            $this->creditLimitService->updateDefaultCreditLimit(
                $customer->getId(),
                0,
                $oldCustomerGroup ? __('Customer group has been changed') : ''
            );
        }

        return true;
    }

    /**
     * Check if credit limit the same for both customers
     *
     * @param int $oldCustomerGroup
     * @param CustomerInterface $customer
     * @return bool
     * @throws LocalizedException
     */
    private function isCreditLimitTheSame($oldCustomerGroup, $customer)
    {
        $oldCreditLimit = $this->customerGroupService->getCreditLimit(
            $oldCustomerGroup,
            $customer->getWebsiteId()
        );
        $newCreditLimit = $this->customerGroupService->getCreditLimit(
            $customer->getGroupId(),
            $customer->getWebsiteId()
        );

        return $oldCreditLimit == $newCreditLimit;
    }
}
