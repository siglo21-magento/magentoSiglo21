<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Source\Admin\User;

use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use Magento\User\Model\ResourceModel\User\Collection as UserCollection;
use Magento\Framework\DB\Select;

/**
 * Class Loader
 *
 * @package Aheadworks\Ca\Model\Source\Admin\User
 */
class Loader
{
    /**
     * Inactive label
     */
    const INACTIVE_LABEL = '(Inactive)';

    /**
     * @var UserCollectionFactory
     */
    private $userCollectionFactory;

    /**
     * @param UserCollectionFactory $userCollectionFactory
     */
    public function __construct(
        UserCollectionFactory $userCollectionFactory
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
    }

    /**
     * Prepare user data
     */
    public function load()
    {
        $userCollection = $this->prepareUserCollection();
        return $userCollection->getItems();
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
