<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Setup;

use Aheadworks\Ca\Setup\Updater\Schema\Updater;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 *
 * @package Aheadworks\Ca\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Updater
     */
    private $updater;

    /**
     * @param Updater $updater
     */
    public function __construct(Updater $updater)
    {
        $this->updater = $updater;
    }

    /**
     * @inheritdoc
     *
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($context->getVersion() && version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->updater->upgradeTo110($setup);
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.3.0', '<')) {
            $this->updater->install130($setup);
        }
    }
}
