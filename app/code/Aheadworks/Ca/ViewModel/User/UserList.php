<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\ViewModel\User;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Api\Data\CustomerSearchResultsInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;

/**
 * Class UserList
 * @package Aheadworks\Ca\ViewModel\User
 */
class UserList implements ArgumentInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var CustomerSearchResultsInterface|null
     */
    private $customerSearchResults;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Retrieves search criteria builder
     *
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder()
    {
        return $this->searchCriteriaBuilder;
    }

    /**
     * Retrieve customer search results
     *
     * @return CustomerSearchResultsInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSearchResults()
    {
        if (null === $this->customerSearchResults) {
            $companyUser = $this->companyUserManagement->getCurrentUser();
            $companyId = $companyUser->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();

            $sortOrderByStatus = $this->sortOrderBuilder
                ->setField('aw_ca_company_user_is_activated')
                ->setDirection(SortOrder::SORT_DESC)
                ->create();

            $sortOrderById = $this->sortOrderBuilder
                ->setField('entity_id')
                ->setDirection(SortOrder::SORT_ASC)
                ->create();

            $this->searchCriteriaBuilder
                ->addSortOrder($sortOrderByStatus)
                ->addSortOrder($sortOrderById)
                ->addFilter('aw_ca_customer_by_company_id', $companyId)
                ->addFilter('aw_ca_customer_is_activated_join', true);

            $this->customerSearchResults = $this->customerRepository->getList($this->searchCriteriaBuilder->create());
        }

        return $this->customerSearchResults;
    }
}
