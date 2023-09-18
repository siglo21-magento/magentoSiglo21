<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Customer\Checker\EmailAvailability;

use Aheadworks\Ca\Api\Data\EmailAvailabilityResultInterfaceFactory;
use Aheadworks\Ca\Api\Data\EmailAvailabilityResultInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Config\Share;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Checker
 *
 * @package Aheadworks\Ca\Model\Customer\Checker\EmailAvailability
 */
class Checker
{
    /**
     * @var EmailAvailabilityResultInterfaceFactory
     */
    private $availabilityResultFactory;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var Share
     */
    private $customerShareConfig;

    /**
     * @param EmailAvailabilityResultInterfaceFactory $availabilityResultFactory
     * @param AccountManagementInterface $accountManagement
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CompanyRepositoryInterface $companyRepository
     * @param Share $customerShareConfig
     */
    public function __construct(
        EmailAvailabilityResultInterfaceFactory $availabilityResultFactory,
        AccountManagementInterface $accountManagement,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CompanyRepositoryInterface $companyRepository,
        Share $customerShareConfig
    ) {
        $this->availabilityResultFactory = $availabilityResultFactory;
        $this->accountManagement = $accountManagement;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->companyRepository = $companyRepository;
        $this->customerShareConfig = $customerShareConfig;
    }

    /**
     * Check if email is available
     *
     * @param string $email
     * @param int|null $website
     * @throws LocalizedException
     * @return EmailAvailabilityResultInterface
     */
    public function check($email, $website = null)
    {
        $isAvailableForCustomer = $this->accountManagement->isEmailAvailable($email, $website);
        $isAvailableForCompany = $this->isEmailAvailableForCompany($email, $website);

        return $this->availabilityResultFactory->create(
            [
                'isAvailableForCustomer' => $isAvailableForCustomer,
                'isAvailableForCompany' => $isAvailableForCompany
            ]
        );
    }

    /**
     * Check if email is available for company
     *
     * @param string $email
     * @param int|null $website
     * @return bool
     * @throws LocalizedException
     */
    public function isEmailAvailableForCompany($email, $website)
    {
        if ($this->customerShareConfig->isWebsiteScope()) {
            $this->searchCriteriaBuilder->addFilter('website', $website);
        }
        $this->searchCriteriaBuilder->addFilter(CompanyInterface::EMAIL, $email);
        $companyList = $this->companyRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return empty($companyList);
    }
}
