<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Customer\Backend;

use Magento\User\Model\ResourceModel\User\Collection;
use Magento\User\Model\ResourceModel\User\CollectionFactory;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Ui\Component\Listing\Customer\Bookmark\OutstandingBalance;
use Magento\User\Model\User as AdminUser;

/**
 * Class BookmarkInstaller
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Backend
 */
class BookmarkInstaller
{
    /**
     * @var CollectionFactory
     */
    private $userCollectionFactory;

    /**
     * @var OutstandingBalance
     */
    private $outstandingBalanceBookmark;

    /**
     * @var BookmarkRepositoryInterface
     */
    private $bookmarkRepository;

    /**
     * @param CollectionFactory $userCollectionFactory
     * @param OutstandingBalance $outstandingBalanceBookmark
     * @param BookmarkRepositoryInterface $bookmarkRepository
     */
    public function __construct(
        CollectionFactory $userCollectionFactory,
        OutstandingBalance $outstandingBalanceBookmark,
        BookmarkRepositoryInterface $bookmarkRepository
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
        $this->outstandingBalanceBookmark = $outstandingBalanceBookmark;
        $this->bookmarkRepository = $bookmarkRepository;
    }

    /**
     * Create necessary bookmarks
     *
     * @throws LocalizedException
     */
    public function install()
    {
        /** @var Collection $userCollection */
        $userCollection = $this->userCollectionFactory->create();
        $adminUsers = $userCollection->getItems();

        /** @var AdminUser $adminUser */
        foreach ($adminUsers as $adminUser) {
            $bookmark = $this->outstandingBalanceBookmark->create($adminUser);
            $this->bookmarkRepository->save($bookmark);
        }
    }
}
