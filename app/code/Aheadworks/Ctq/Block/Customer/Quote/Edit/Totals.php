<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Customer\Quote\Edit;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Aheadworks\Ctq\Model\Quote\Customer\ConfigProvider\Composite as CompositeConfigProvider;

/**
 * Class Totals
 * @package Aheadworks\Ctq\Block\Customer\Quote\Edit
 */
class Totals extends Template
{
    /**
     * @var LayoutProcessorInterface[]
     */
    private $layoutProcessors;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @var CompositeConfigProvider
     */
    private $configProvider;

    /**
     * @param Context $context
     * @param JsonSerializer $serializer
     * @param CompositeConfigProvider $configProvider
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        Context $context,
        JsonSerializer $serializer,
        CompositeConfigProvider $configProvider,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->serializer = $serializer;
        $this->configProvider = $configProvider;
        $this->layoutProcessors = $layoutProcessors;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        return $this->serializer->serialize($this->jsLayout);
    }

    /**
     * Retrieve serialized checkout config
     *
     * @return bool|string
     */
    public function getCheckoutConfig()
    {
        $config = $this->configProvider->getConfig();
        return $this->serializer->serialize($config);
    }
}
