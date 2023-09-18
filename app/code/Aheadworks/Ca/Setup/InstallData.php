<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SampleData\Executor as SampleDataExecutor;
use Aheadworks\Ca\Setup\SampleData\Installer as SampleDataInstaller;

/**
 * Class InstallData
 *
 * @package Aheadworks\Ca\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var SampleDataExecutor
     */
    private $sampleDataExecutor;

    /**
     * @var SampleDataInstaller
     */
    private $sampleDataInstaller;

    /**
     * @param SampleDataExecutor $sampleDataExecutor
     * @param SampleDataInstaller $sampleDataInstaller
     */
    public function __construct(
        SampleDataExecutor $sampleDataExecutor,
        SampleDataInstaller $sampleDataInstaller
    ) {
        $this->sampleDataExecutor = $sampleDataExecutor;
        $this->sampleDataInstaller = $sampleDataInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->installSampleData();

        $setup->endSetup();
    }

    /**
     * Install sample data
     *
     * @return $this
     */
    private function installSampleData()
    {
        $this->sampleDataExecutor->exec($this->sampleDataInstaller);

        return $this;
    }
}
