<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Backend\Block\Template\Context;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as SessionQuote;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater as QuoteUpdater;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Total\Provider as TotalProvider;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Total\Renderer as TotalRenderer;
use Magento\Framework\Phrase;

/**
 * Class Totals
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit
 */
class Totals extends AbstractEdit
{
    /**
     * @var TotalProvider
     */
    private $totalProvider;

    /**
     * @var TotalRenderer
     */
    private $totalRenderer;

    /**
     * @param Context $context
     * @param SessionQuote $sessionQuote
     * @param QuoteUpdater $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param TotalProvider $totalProvider
     * @param TotalRenderer $totalRenderer
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionQuote $sessionQuote,
        QuoteUpdater $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        TotalProvider $totalProvider,
        TotalRenderer $totalRenderer,
        array $data = []
    ) {
        $this->totalProvider = $totalProvider;
        $this->totalRenderer = $totalRenderer;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('aw_ctq_quote_edit_quote_totals');
    }

    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Quote Totals');
    }

    /**
     * Get header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-totals';
    }

    /**
     * Render quote totals
     *
     * @param string|null $area
     * @param int $colspan
     * @return string
     */
    public function renderTotals($area = null, $colspan = 1)
    {
        $totals = $this->totalProvider->getQuoteTotals($this->getQuote());
        return $this->totalRenderer->render($totals, $area, $colspan);
    }
}
