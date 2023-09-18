<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\QuotedItems;

use Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\AbstractEdit;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Backend\Block\Widget\Button as WidgetButton;
use Magento\Store\Model\Store;
use Magento\Tax\Helper\Data as TaxData;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater as QuoteUpdater;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as SessionQuote;
use Magento\Backend\Block\Template\Context;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Total\Calculator as TotalCalculator;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class Grid
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\QuotedItems
 * @method \Aheadworks\Ctq\ViewModel\Quote\Edit\CurrentQuote getQuoteViewModel()
 */
class Grid extends AbstractEdit
{
    /**
     * @var TaxData
     */
    private $taxData;

    /**
     * @var StockStateInterface
     */
    private $stockState;

    /**
     * @var TotalCalculator
     */
    private $totalCalculator;

    /**
     * @param Context $context
     * @param SessionQuote $sessionQuote
     * @param QuoteUpdater $quoteUpdater
     * @param PriceCurrencyInterface $priceCurrency
     * @param TaxData $taxData
     * @param StockStateInterface $stockState
     * @param TotalCalculator $totalCalculator
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionQuote $sessionQuote,
        QuoteUpdater $quoteUpdater,
        PriceCurrencyInterface $priceCurrency,
        TaxData $taxData,
        StockStateInterface $stockState,
        TotalCalculator $totalCalculator,
        array $data = []
    ) {
        $this->taxData = $taxData;
        $this->stockState = $stockState;
        $this->totalCalculator = $totalCalculator;
        parent::__construct($context, $sessionQuote, $quoteUpdater, $priceCurrency, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('aw_ctq_quote_edit_search_grid');
    }

    /**
     * Get items
     *
     * @return Item[]
     */
    public function getItems()
    {
        $items = $this->getParentBlock()->getItems();
        $oldSuperMode = $this->getQuote()->getIsSuperMode();
        $this->getQuote()->setIsSuperMode(false);
        foreach ($items as $item) {
            $item->setQty($item->getQty());

            if (!$item->getMessage()) {
                $stockItemToCheck = [];

                $childItems = $item->getChildren();
                if (count($childItems)) {
                    foreach ($childItems as $childItem) {
                        $stockItemToCheck[] = $childItem->getProduct()->getId();
                    }
                } else {
                    $stockItemToCheck[] = $item->getProduct()->getId();
                }

                foreach ($stockItemToCheck as $productId) {
                    $check = $this->stockState->checkQuoteItemQty(
                        $productId,
                        $item->getQty(),
                        $item->getQty(),
                        $item->getQty(),
                        $this->getQuote()->getStore()->getWebsiteId()
                    );
                    $item->setMessage($check->getMessage());
                    $item->setHasError($check->getHasError());
                }
            }

            if ($item->getProduct()->getStatus() == ProductStatus::STATUS_DISABLED) {
                $item->setMessage(__('This product is disabled.'));
                $item->setHasError(true);
            }
        }
        $this->getQuote()->setIsSuperMode($oldSuperMode);
        return $items;
    }

    /**
     * Get total calculator
     *
     * @return TotalCalculator
     */
    public function getTotalCalculator()
    {
        return $this->totalCalculator;
    }

    /**
     * Get item editable price
     *
     * @param Item $item
     * @return float
     */
    public function getItemEditablePrice($item)
    {
        return $item->getCalculationPrice() * 1;
    }

    /**
     * Get original editable price
     *
     * @param Item $item
     * @return float
     */
    public function getOriginalEditablePrice($item)
    {
        if ($item->hasOriginalCustomPrice()) {
            $result = $item->getOriginalCustomPrice() * 1;
        } elseif ($item->hasCustomPrice()) {
            $result = $item->getCustomPrice() * 1;
        } else {
            if ($this->taxData->priceIncludesTax($this->getStore())) {
                $result = $item->getPriceInclTax() * 1;
            } else {
                $result = $item->getOriginalPrice() * 1;
            }
        }
        return $result;
    }

    /**
     * Get item original price
     *
     * @param Item $item
     * @return float
     */
    public function getItemOrigPrice($item)
    {
        return $this->convertPrice($item->getPrice());
    }

    /**
     * Define if specified item has already applied custom price
     *
     * @param Item $item
     * @return bool
     */
    public function usedCustomPriceForItem($item)
    {
        return $item->hasCustomPrice();
    }

    /**
     * Define if custom price can be applied for specified item
     *
     * @param Item $item
     * @return bool
     */
    public function canApplyCustomPrice($item)
    {
        return false;
        //return !$item->isChildrenCalculated();
    }

    /**
     * Display subtotal including tax
     *
     * @param Item $item
     * @return string
     */
    public function displaySubtotalInclTax($item)
    {
        if ($item->getTaxBeforeDiscount()) {
            $tax = $item->getTaxBeforeDiscount();
        } else {
            $tax = $item->getTaxAmount() ? $item->getTaxAmount() : 0;
        }
        return $this->formatPrice($item->getRowTotal() + $tax);
    }

    /**
     * Display original price including tax
     *
     * @param Item $item
     * @return float
     */
    public function displayOriginalPriceInclTax($item)
    {
        $tax = 0;
        if ($item->getTaxPercent()) {
            $tax = $item->getPrice() * ($item->getTaxPercent() / 100);
        }
        return $this->convertPrice($item->getPrice() + $tax / $item->getQty());
    }

    /**
     * Display row total with discount including tax
     *
     * @param Item $item
     * @return string
     */
    public function displayRowTotalWithDiscountInclTax($item)
    {
        $tax = $item->getTaxAmount() ? $item->getTaxAmount() : 0;
        return $this->formatPrice($item->getRowTotal() - $item->getDiscountAmount() + $tax);
    }

    /**
     * Get store
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->getQuote()->getStore();
    }

    /**
     * Return html button which calls configure window
     *
     * @param Item $item
     * @return string
     * @throws LocalizedException
     */
    public function getConfigureButtonHtml($item)
    {
        $product = $item->getProduct();

        $options = ['label' => __('Configure')];
        if ($product->canConfigure()) {
            $options['onclick'] = sprintf('quote.showQuoteItemConfiguration(%s)', $item->getId());
        } else {
            $options['class'] = ' disabled';
            $options['title'] = __('This product does not have any configurable options');
        }

        return $this->getLayout()
            ->createBlock(WidgetButton::class)
            ->setData($options)
            ->toHtml();
    }

    /**
     * Get order item extra info block
     *
     * @param Item $item
     * @return AbstractBlock
     * @throws LocalizedException
     */
    public function getItemExtraInfo($item)
    {
        return $this->getLayout()->getBlock('quote_item_extra_info')->setItem($item);
    }

    /**
     * Get tier price html
     *
     * @param Item $item
     * @return string
     * @throws LocalizedException
     */
    public function getTierHtml($item)
    {
        $block = $this->getLayout()->getBlock('item_tier_price_info');
        $block->setItem($item);
        return $block->toHtml();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/updateBlock',
            ['block' => 'search_grid']
        );
    }

    /**
     * Return url to product edit page
     *
     * @param Item $item
     * @return string
     */
    public function getLinkToProductEditPage(Item $item)
    {
        return $this->getUrl(
            'catalog/product/edit',
            ['id' => $item->getProductId()]
        );
    }

    /**
     * Return stock count
     *
     * @param Item $item
     * @return int
     */
    public function getStockCount(Item $item)
    {
        if ($item->getProductType() == Configurable::TYPE_CODE) {
            foreach ($item->getQuote()->getAllItems() as $quoteItem) {
                if ($quoteItem->getParentItemId() == $item->getId()) {
                    $item = $quoteItem;
                    break;
                }
            }
        }
        $productId = $item->getProduct()->getId();
        return $this->stockState->getStockQty($productId);
    }

    /**
     * Return rounded price
     *
     * @param float $price
     * @return false|float
     */
    public function getRoundedPrice($price)
    {
        return round($price, PriceCurrencyInterface::DEFAULT_PRECISION);
    }

    /**
     * Retrieve currency symbol
     *
     * @param Quote $quote
     * @return string
     */
    public function getCurrencySymbol($quote)
    {
        return $this->priceCurrency->getCurrencySymbol(null, $quote->getCurrency()->getBaseCurrencyCode());
    }
}
