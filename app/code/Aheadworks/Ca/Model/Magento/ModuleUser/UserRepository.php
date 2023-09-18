<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Magento\ModuleUser;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\User\Api\Data\UserInterface;
use Magento\User\Api\Data\UserInterfaceFactory;
use Magento\User\Model\ResourceModel\User as UserResource;

/**
 * Class UserRepository
 *
 * @package Aheadworks\Ca\Model\Magento\ModuleUser
 */
class UserRepository
{
    /**
     * @var UserResource
     */
    private $resource;

    /**
     * @var UserInterfaceFactory
     */
    private $userFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param UserResource $resource
     * @param UserInterfaceFactory $userFactory
     */
    public function __construct(
        UserResource $resource,
        UserInterfaceFactory $userFactory
    ) {
        $this->resource = $resource;
        $this->userFactory = $userFactory;
    }

    /**
     * Get Magento Backend user by ID
     *
     * @param int $userId
     * @return UserInterface
     * @throws NoSuchEntityException
     */
    public function getById($userId)
    {
        if (!isset($this->registry[$userId])) {
            /** @var UserInterface $user */
            $user = $this->userFactory->create();
            $this->resource->load($user, $userId);
            if (!$user->getId()) {
                throw NoSuchEntityException::singleField('id', $userId);
            }
            $this->registry[$userId] = $user;
        }
        return $this->registry[$userId];
    }
}
