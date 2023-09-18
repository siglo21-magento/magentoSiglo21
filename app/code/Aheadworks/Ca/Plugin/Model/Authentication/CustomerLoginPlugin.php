<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Plugin\Model\Authentication;

use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CustomerLoginPlugin
 * @package Aheadworks\Ca\Plugin\Model\Authentication
 */
class CustomerLoginPlugin
{
    /**
     * @var SellerCompanyManagementInterface
     */
    private $companyManagement;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param SellerCompanyManagementInterface $companyManagement
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        SellerCompanyManagementInterface $companyManagement,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->companyManagement = $companyManagement;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param AuthenticationInterface $subject
     * @param $customerId
     * @return null
     * @throws LocalizedException
     */
    public function beforeIsLocked($subject, $customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (\Exception $e) {
            return [$customerId];
        }

        if ($companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            if ($this->companyManagement->isBlockedCompany($companyUser->getCompanyId())
                || !$customer->getExtensionAttributes()->getAwCaCompanyUser()->getIsActivated()
            ) {
                throw new LocalizedException(__('This account is blocked.'));
            }
        }

        return null;
    }
}
