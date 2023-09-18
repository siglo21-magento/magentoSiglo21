<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Service;

use Aheadworks\Ca\Api\Data\GroupInterface;
use Aheadworks\Ca\Api\Data\GroupInterfaceFactory;
use Aheadworks\Ca\Api\GroupManagementInterface;
use Aheadworks\Ca\Api\GroupRepositoryInterface;
use Aheadworks\Ca\Model\ResourceModel\Group as GroupResource;

/**
 * Class GroupService
 * @package Aheadworks\Ca\Model\Service
 */
class GroupService implements GroupManagementInterface
{
    /**
     * @var GroupResource
     */
    private $groupResource;

    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var GroupInterfaceFactory
     */
    private $groupFactory;

    /**
     * @param GroupResource $groupResource
     * @param GroupRepositoryInterface $groupRepository
     * @param GroupInterfaceFactory $groupFactory
     */
    public function __construct(
        GroupResource $groupResource,
        GroupRepositoryInterface $groupRepository,
        GroupInterfaceFactory $groupFactory
    ) {
        $this->groupResource = $groupResource;
        $this->groupRepository = $groupRepository;
        $this->groupFactory = $groupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createDefaultGroup()
    {
        /** @var GroupInterface $group */
        $group = $this->groupFactory->create();
        $group->setParentId(0);

        return $this->saveGroup($group);
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveGroup($group)
    {
        if (!$group->getPath()) {
            $group->setPath($this->groupResource->getNextIncrementId());
        }
        $path = trim(trim($group->getPath(), '/')) . '/';
        $group->setPath($path);
        $this->groupRepository->save($group);

        return $group;
    }
}
