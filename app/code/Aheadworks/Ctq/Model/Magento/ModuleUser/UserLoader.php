<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Magento\ModuleUser;

use Aheadworks\Ctq\Model\Magento\ModuleUser\UserLoader\AclChecker;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use Magento\User\Model\ResourceModel\User\Collection as UserCollection;
use Magento\Framework\DB\Select;

/**
 * Class UserLoader
 *
 * @package Aheadworks\Ctq\Model\Magento\ModuleUser
 */
class UserLoader
{
    /**
     * Acl resource
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ctq::quotes';

    /**
     * Inactive label
     */
    const INACTIVE_LABEL = '(Inactive)';

    /**
     * @var UserCollectionFactory
     */
    private $userCollectionFactory;

    /**
     * @var AclChecker
     */
    private $aclChecker;

    /**
     * @param UserCollectionFactory $userCollectionFactory
     * @param AclChecker $aclChecker
     */
    public function __construct(
        UserCollectionFactory $userCollectionFactory,
        AclChecker $aclChecker
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
        $this->aclChecker = $aclChecker;
    }

    /**
     * Prepare user data
     */
    public function load()
    {
        $userCollection = $this->prepareUserCollection();
        $users = [];
        foreach ($userCollection->getItems() as $user) {
            if ($this->aclChecker->isAllowed($user, self::ADMIN_RESOURCE)) {
                $users[] = $user;
            }
        }
        return $users;
    }

    /**
     * Prepare user collection
     *
     * @return UserCollection
     */
    private function prepareUserCollection()
    {
        $userCollection = $this->userCollectionFactory->create();
        $userCollection
            ->getSelect()
            ->reset(Select::COLUMNS)
            ->columns([
                'user_id' => 'main_table.user_id',
                'user_fullname' => 'CONCAT(main_table.firstname, " ", main_table.lastname)',
                'is_active' => 'main_table.is_active'
            ]);
        $this->addInactiveLabelToUserName($userCollection);

        return $userCollection;
    }

    /**
     * Adding "Inactive" label to the name of inactive user
     *
     * @param UserCollection $userCollection
     */
    private function addInactiveLabelToUserName($userCollection)
    {
        $inactiveLabel = self::INACTIVE_LABEL;
        foreach ($userCollection->getItems() as $user) {
            if (!$user->getIsActive()) {
                $user->setUserFullname($user->getUserFullname() . ' ' . __($inactiveLabel));
            }
        }
    }
}
