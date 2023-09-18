<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Admin\Quote\Total;

use Magento\Framework\View\Element\BlockInterface;
use Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\Totals\DefaultTotals;
use Magento\Sales\Model\Config as SalesConfig;
use Magento\Framework\View\LayoutInterface;
use Magento\Quote\Model\Quote\Address\Total;

/**
 * Class Renderer
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Quote\Total
 */
class Renderer
{
    /**
     * Default renderer
     *
     * @var string
     */
    private $defaultRenderer = DefaultTotals::class;

    /**
     * @var array
     */
    private $totals = [];

    /**
     * @var SalesConfig
     */
    private $salesConfig;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @param SalesConfig $salesConfig
     * @param LayoutInterface $layout
     */
    public function __construct(
        SalesConfig $salesConfig,
        LayoutInterface $layout
    ) {
        $this->salesConfig = $salesConfig;
        $this->layout = $layout;
    }

    /**
     * Render totals
     *
     * @param array $totals
     * @param string|null $area
     * @param int $colspan
     * @return string
     */
    public function render($totals, $area = null, $colspan = 1)
    {
        $this->totals = $totals;
        $html = '';
        foreach ($totals as $total) {
            if ($total->getArea() != $area && $area != -1) {
                continue;
            }
            $html .= $this->renderTotal($total, $area, $colspan);
        }
        return $html;
    }

    /**
     * Render total
     *
     * @param Total $total
     * @param string|null $area
     * @param int $colspan
     * @return string
     */
    private function renderTotal($total, $area = null, $colspan = 1)
    {
        return $this->getTotalRenderer($total->getCode())
            ->setTotal($total)
            ->setColspan($colspan)
            ->setRenderingArea($area === null ? -1 : $area)
            ->toHtml();
    }

    /**
     * Get total renderer
     *
     * @param string $code
     * @return bool|BlockInterface
     * @return array|bool|BlockInterface|string
     */
    private function getTotalRenderer($code)
    {
        $blockName = $code . '_total_renderer';
        $block = $this->layout->getBlock($blockName);
        if (!$block) {
            $configRenderer = $this->salesConfig->getTotalsRenderer('quote', 'totals', $code);
            if (empty($configRenderer)) {
                $block = $this->defaultRenderer;
            } else {
                $block = $configRenderer;
            }

            $block = $this->layout->createBlock($block, $blockName);
        }

        $block->setTotals($this->totals);
        return $block;
    }
}
