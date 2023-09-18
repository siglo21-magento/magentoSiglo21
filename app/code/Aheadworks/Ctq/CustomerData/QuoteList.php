<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\CustomerData;

use Aheadworks\Ctq\Model\QuoteList\Permission\Checker;
use Aheadworks\Ctq\Model\QuoteList\Provider;
use Magento\Checkout\CustomerData\Cart;
use Magento\Checkout\CustomerData\ItemPoolInterface;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\LayoutInterface;
use Magento\Checkout\Helper\Data as CheckoutHelper;
use Magento\Catalog\Model\ResourceModel\Url as CatalogUrl;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Model\Cart as CheckoutCart;

/**
 * Class QuoteList
 * @package Aheadworks\Ctq\CustomerData
 */
class QuoteList extends Cart implements SectionSourceInterface
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var Checker
     */
    private $checker;

    /**
     * @param CheckoutSession $checkoutSession
     * @param CatalogUrl $catalogUrl
     * @param CheckoutCart $checkoutCart
     * @param CheckoutHelper $checkoutHelper
     * @param ItemPoolInterface $itemPoolInterface
     * @param LayoutInterface $layout
     * @param Provider $provider
     * @param Checker $checker
     * @param array $data
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CatalogUrl $catalogUrl,
        CheckoutCart $checkoutCart,
        CheckoutHelper $checkoutHelper,
        ItemPoolInterface $itemPoolInterface,
        LayoutInterface $layout,
        Provider $provider,
        Checker $checker,
        array $data = []
    ) {
        parent::__construct(
            $checkoutSession,
            $catalogUrl,
            $checkoutCart,
            $checkoutHelper,
            $itemPoolInterface,
            $layout,
            $data
        );
        $this->provider = $provider;
        $this->checker = $checker;
    }

    /**
     * {@inheritDoc}
     */
    public function getSectionData()
    {
        $sectionData = ['items' => []];

        if ($this->checker->isAllowed()) {
            try {
                $sectionData = parent::getSectionData();
                $sectionData['summary_count'] = $this->getQuote()->getItemsCount() * 1;
                $sectionData['summary_qty'] = $this->getQuote()->getItemsQty() * 1;
            } catch (LocalizedException $e) {
            }
        }

        return $sectionData;
    }

    /**
     * {@inheritDoc}
     */
    protected function getQuote()
    {
        return $this->provider->getQuote();
    }

    /**
     * {@inheritDoc}
     */
    protected function getSummaryCount()
    {
        return $this->getQuote()->getItemsQty();
    }
}
