<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Ui\Component\Form\Company;

use Magento\Ui\Component\Form\Field;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;

/**
 * Class AllowQuoteField
 *
 * @package Aheadworks\Ca\Ui\Component\Form\Company
 */
class AllowQuoteField extends Field
{
    /**
     * @var Manager
     */
    private $thirdPartyModuleManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Manager $thirdPartyModuleManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Manager $thirdPartyModuleManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->thirdPartyModuleManager->isAwCtqModuleEnabled()) {
            $config = $this->getData('config');
            $config['visible'] = false;
            $this->setData('config', $config);
        }
    }
}
