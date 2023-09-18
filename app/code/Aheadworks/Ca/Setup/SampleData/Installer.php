<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Setup\SampleData;

use Magento\Framework\Setup\SampleData\InstallerInterface as SampleDataInstallerInterface;

/**
 * Class Installer
 * @package Aheadworks\Ca\Setup\SampleData
 */
class Installer implements SampleDataInstallerInterface
{
    /**
     * @var SampleDataInstallerInterface[]
     */
    private $installers;

    /**
     * @param SampleDataInstallerInterface[] $installers
     */
    public function __construct(
        array $installers = []
    ) {
        $this->installers = $installers;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        foreach ($this->installers as $installer) {
            $installer->install();
        }
    }
}
