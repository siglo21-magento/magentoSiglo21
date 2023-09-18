<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Payment;

use Magento\Quote\Model\Quote;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class AvailabilityChecker
 *
 * @package Aheadworks\CreditLimit\Model\Payment
 */
class AvailabilityChecker
{
    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param CustomerManagementInterface $customerManagement
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerManagementInterface $customerManagement,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerManagement = $customerManagement;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Check if credit limit is available on frontend
     *
     * @param Quote|null $quote
     * @return bool
     */
    public function isAvailableOnFrontend($quote)
    {
        return $quote
            && $quote->getCustomerId()
            && $this->customerManagement->isCreditLimitAvailable($quote->getCustomerId())
            && $this->customerManagement->getCreditAvailableAmount($quote->getCustomerId()) != 0;
    }

    /**
     * Check if credit limit is available in backend
     *
     * @param Quote|null $quote
     * @return bool
     */
    public function isAvailableInAdmin($quote)
    {
        $isAvailable = $this->isAvailableOnFrontend($quote);
        if ($isAvailable) {
            try {
                $customer = $this->customerRepository->getById($quote->getCustomerId());
                if ($quote->getCustomerGroupId() != $customer->getGroupId()) {
                    $isAvailable = false;
                }
            } catch (\Exception $exception) {
                $isAvailable = false;
            }
        }

        return $isAvailable;
    }
}
