<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Ui\Component\Form\Company;

use Magento\Ui\Component\Form\Fieldset;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;

/**
 * Class AllowPayRest
 * @package Aheadworks\Ca\Ui\Component\Form\Company
 */
class AllowPayRest extends Fieldset
{
    /**
     * @var Manager
     */
    private $thirdPartyModuleManager;

    /**
     * @param ContextInterface $context
     * @param Manager $thirdPartyModuleManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Manager $thirdPartyModuleManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->thirdPartyModuleManager->isAwPayRestModuleEnabled()) {
            $config = $this->getData('config');
            $config['visible'] = false;
            $this->setData('config', $config);
        }
    }
}
