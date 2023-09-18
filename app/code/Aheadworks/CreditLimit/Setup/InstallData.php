<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Aheadworks\CreditLimit\Model\Customer\Backend\BookmarkInstaller;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class InstallData
 *
 * @package Aheadworks\CreditLimit\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var BookmarkInstaller
     */
    private $bookmarkInstaller;

    /**
     * @param BookmarkInstaller $bookmarkInstaller
     */
    public function __construct(
        BookmarkInstaller $bookmarkInstaller
    ) {
        $this->bookmarkInstaller = $bookmarkInstaller;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->bookmarkInstaller->install();
    }
}
