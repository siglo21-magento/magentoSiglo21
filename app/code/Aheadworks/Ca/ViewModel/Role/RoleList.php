<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\ViewModel\Role;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\ViewModel\ListViewModelInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ca\Api\Data\RoleSearchResultsInterface;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Aheadworks\Ca\Api\Data\RoleInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * Class RoleList
 * @package Aheadworks\Ca\ViewModel\Role
 */
class RoleList implements ArgumentInterface, ListViewModelInterface
{
    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var RoleSearchResultsInterface|null
     */
    private $roleSearchResults;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param RoleRepositoryInterface $roleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        RoleRepositoryInterface $roleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->roleRepository = $roleRepository;
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
     * Retrieve role search results
     *
     * @return RoleSearchResultsInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSearchResults()
    {
        if (null === $this->roleSearchResults) {
            $companyUser = $this->companyUserManagement->getCurrentUser();
            $companyId = $companyUser->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();

            $sortOrder = $this->sortOrderBuilder
                ->setField(RoleInterface::ID)
                ->setDirection(SortOrder::SORT_ASC)
                ->create();

            $this->searchCriteriaBuilder
                ->addFilter(RoleInterface::COMPANY_ID, ['eq' => $companyId])
                ->addSortOrder($sortOrder);

            $this->roleSearchResults = $this->roleRepository->getList($this->searchCriteriaBuilder->create());
        }

        return $this->roleSearchResults;
    }
}
