<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\History;

use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterfaceFactory;
use Aheadworks\Ctq\Block\History\Action\DefaultRenderer;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Render
 * @package Aheadworks\Ctq\Block\History
 * @method Render setHistory(HistoryInterface $history)
 * @method \Aheadworks\Ctq\ViewModel\Customer\Quote getQuoteViewModel()
 * @method \Aheadworks\Ctq\ViewModel\History\History getHistoryViewModel()
 * @method bool|null getIsEmailForSeller()
 * @method Render setIsEmailForSeller(bool $value)
 */
class Render extends Template
{
    /**
     * @var HistoryInterfaceFactory
     */
    private $historyFactory;

    /**
     * @param Context $context
     * @param HistoryInterfaceFactory $historyFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        HistoryInterfaceFactory $historyFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->historyFactory = $historyFactory;
    }

    /**
     * Retrieve action html
     *
     * @param HistoryActionInterface $action
     * @return string
     */
    public function getActionHtml($action)
    {
        /** @var DefaultRenderer $block */
        $block = $this->getActionRenderer($action->getType());
        $block
            ->setAction($action)
            ->setHistory($this->getHistory())
            ->setIsEmailForSeller($this->getIsEmailForSeller())
            ->setQuoteViewModel($this->getQuoteViewModel())
            ->setHistoryViewModel($this->getHistoryViewModel());

        return $block->toHtml();
    }

    /**
     * Retrieve action renderer
     *
     * @param string $type
     * @return bool|\Magento\Framework\View\Element\AbstractBlock
     */
    public function getActionRenderer($type)
    {
        /** @var \Magento\Framework\View\Element\RendererList $rendererList */
        $rendererList = $this->getChildBlock('action.renderer.list');
        if (!$rendererList) {
            throw new \RuntimeException('Renderer list for block "' . $this->getNameInLayout() . '" is not defined');
        }

        $renderer = $rendererList->getRenderer($type, 'default');
        $renderer->setRenderedBlock($this);

        return $renderer;
    }

    /**
     * Retrieve history
     *
     * @return HistoryInterface
     */
    public function getHistory()
    {
        $history = $this->getData('history') ? : $this->historyFactory->create();

        return $history;
    }
}
