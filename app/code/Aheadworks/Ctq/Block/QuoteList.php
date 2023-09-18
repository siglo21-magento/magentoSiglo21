<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block;

use Aheadworks\Ctq\Model\QuoteList\Provider;
use Magento\Checkout\Block\Cart\AbstractCart;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

/**
 * Class QuoteList
 * @package Aheadworks\Ctq\Block
 */
class QuoteList extends AbstractCart
{
    /**
     * @var Provider
     */
    private $quoteProvider;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param Provider $quoteProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        Provider $quoteProvider,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->quoteProvider = $quoteProvider;
    }

    /**
     * Get Ctq Quote instance
     *
     * @return CartInterface|Quote|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuote()
    {
        if ($this->quoteProvider->getQuoteId()) {
            $this->_quote = $this->quoteProvider->getQuote();
        }

        return $this->_quote;
    }

    /**
     * Get all cart items
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    /**
     * Get all cart items count
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getItemsCount()
    {
        return $this->getQuote()->getItemsCount();
    }
}
