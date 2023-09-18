<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\Customer\Quote\Edit;

use Magento\Catalog\Helper\Product\ConfigurationPool;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;

/**
 * Class Item
 * @package Aheadworks\Ctq\ViewModel\Customer\Quote\Edit
 */
class Item implements ArgumentInterface
{
    /**
     * @var ConfigurationPool
     */
    private $configurationPool;

    /**
     * @var Configuration
     */
    private $productConfiguration;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var InterpretationStrategyInterface
     */
    private $messageInterpretationStrategy;

    /**
     * @param ConfigurationPool $configurationPool
     * @param Configuration $productConfiguration
     * @param ManagerInterface $messageManager
     * @param InterpretationStrategyInterface $messageInterpretationStrategy
     */
    public function __construct(
        ConfigurationPool $configurationPool,
        Configuration $productConfiguration,
        ManagerInterface $messageManager,
        InterpretationStrategyInterface $messageInterpretationStrategy
    ) {
        $this->configurationPool = $configurationPool;
        $this->productConfiguration = $productConfiguration;
        $this->messageManager = $messageManager;
        $this->messageInterpretationStrategy = $messageInterpretationStrategy;
    }

    /**
     * Retrieve list of all options for product
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return array
     */
    public function getOptionList($item)
    {
        return $this->configurationPool->getByProductType($item->getProductType())->getOptions($item);
    }

    /**
     * Retrieve formatted value
     *
     * @param string|array $optionValue
     * @return array
     */
    public function getFormattedOptionValue($optionValue)
    {
        $params = [
            'max_length' => 55,
            'cut_replacer' => ' <a href="#" class="dots tooltip toggle" onclick="return false">...</a>'
        ];
        return $this->productConfiguration->getFormattedOptionValue($optionValue, $params);
    }

    /**
     * Retrieve item messages
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return array
     */
    public function getMessages($item)
    {
        $messages = [];
        $baseMessages = $item->getMessage(false);
        if ($baseMessages) {
            foreach ($baseMessages as $message) {
                $messages[] = ['text' => $message, 'type' => $item->getHasError() ? 'error' : 'notice'];
            }
        }

        /* @var $collection \Magento\Framework\Message\Collection */
        $collection = $this->messageManager->getMessages(true, 'quote_item' . $item->getId());
        if ($collection) {
            $additionalMessages = $collection->getItems();
            foreach ($additionalMessages as $message) {
                /* @var $message \Magento\Framework\Message\MessageInterface */
                $messages[] = [
                    'text' => $this->messageInterpretationStrategy->interpret($message),
                    'type' => $message->getType()
                ];
            }
        }
        $this->messageManager->getMessages(true, 'quote_item' . $item->getId())->clear();

        return $messages;
    }
}
