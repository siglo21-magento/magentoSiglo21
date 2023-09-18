<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Customer\Export;

use Aheadworks\Ctq\Model\Config;
use Aheadworks\Ctq\ViewModel\Customer\Quote\Edit\ItemFactory as QuoteItemViewModelFactory;
use Magento\Checkout\Model\Cart\CartInterface;
use Magento\Checkout\Block\Cart\Totals as CartTotalsBlock;
use Aheadworks\Ctq\Block\Customer\Quote as CustomerQuote;
use Aheadworks\Ctq\Block\Customer\Quote\Edit\Item as QuoteItemBlock;
use Magento\Cms\Block\Block;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Quote
 * @package Aheadworks\Ctq\Block\Customer\Export
 */
class Quote extends CustomerQuote
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param QuoteItemViewModelFactory $quoteItemViewModelFactory
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        QuoteItemViewModelFactory $quoteItemViewModelFactory,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $quoteItemViewModelFactory, $data);
        $this->config = $config;
    }

    /**
     * Retrieve totals html
     *
     * @param CartInterface $cart
     * @return string
     */
    public function getTotalsHtml($cart)
    {
        /** @var CartTotalsBlock $totalsRenderer */
        $totalsRenderer = $this->getChildBlock('totals.renderer');
        if (!$totalsRenderer) {
            throw new \RuntimeException(
                'Totals renderer for block "' . $this->getNameInLayout() . '" is not defined'
            );
        }
        $totalsRenderer->setData('custom_quote', $cart);
        $totalsHtml =
            $totalsRenderer->renderTotals(null, 4)
            . $totalsRenderer->renderTotals('footer', 4)
        ;
        return $totalsHtml;
    }

    /**
     * @inheritDoc
     */
    public function getItemHtml($item)
    {
        /** @var QuoteItemBlock $block */
        $block = $this->getLayout()->createBlock(
            QuoteItemBlock::class,
            '',
            ['data' => [
                'view_model' => $this->getItemViewModel()
            ]]
        );
        if (!$block) {
            return '';
        }

        $block
            ->setItem($item)
            ->setIsEdit(false)
            ->setIsAllowSorting(false)
            ->setIsExport(true);

        return $block->toHtml();
    }

    /**
     * Retrieve export extra block html
     *
     * @return string
     */
    public function getExportExtraBlockHtml()
    {
        $html = '';
        $blockId = $this->config->getExportExtraBlock();

        if ($blockId) {
            try {
                $html = $this
                    ->getLayout()
                    ->createBlock(Block::class)
                    ->setBlockId($blockId)
                    ->toHtml();
            } catch (LocalizedException $e) {
            }
        }

        return $html;
    }
}
