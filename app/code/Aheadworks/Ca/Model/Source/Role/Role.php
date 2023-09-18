<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Source\Role;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;

/**
 * Class Role
 * @package Aheadworks\Ca\Model\Source\Role
 */
class Role implements OptionSourceInterface
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
     * @param RoleRepositoryInterface $roleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        RoleRepositoryInterface $roleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->roleRepository = $roleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $roles = $this->roleRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        $options = [];
        foreach ($roles as $role) {
            $isDefault = $role[RoleInterface::IS_DEFAULT];
            $options[] = [
                'value' => $role[RoleInterface::ID],
                'label' => $role[RoleInterface::NAME] . ($isDefault ? ' ' . __('(default)') : ''),
                'is_default' => $isDefault,
            ];
        }
        return $options;
    }
}
