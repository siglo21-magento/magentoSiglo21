<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote;

use Aheadworks\Ctq\Api\Data\CartItemInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Cart\Converter;
use Aheadworks\Ctq\Model\Cart\ToQuoteCart;
use Aheadworks\Ctq\Model\Quote\Cart\ToNativeCart;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Total\Calculator as TotalCalculator;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\CurrencyFactory;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\CatalogRuleApplier;

/**
 * Class QuoteManagement
 * @package Aheadworks\Ctq\Model\Quote
 */
class QuoteManagement
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ToQuoteCart
     */
    private $convertToQuoteCart;

    /**
     * @var ToNativeCart
     */
    private $convertToNativeCart;

    /**
     * @var ValidatorFactory
     */
    private $validatorFactory;

    /**
     * @var TotalCalculator
     */
    private $totalCalculator;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var Quote|null
     */
    private $cartCached;

    /**
     * @var CartExtensionFactory
     */
    private $cartExtensionFactory;

    /**
     * @var CatalogRuleApplier
     */
    private $catalogRuleApplier;

    /**
     * @param QuoteRepositoryInterface $quoteRepository
     * @param CartRepositoryInterface $cartRepository
     * @param ToQuoteCart $convertToQuoteCart
     * @param ToNativeCart $convertToNativeCart
     * @param ValidatorFactory $validatorFactory
     * @param TotalCalculator $totalCalculator
     * @param StoreManagerInterface $storeManager
     * @param CurrencyFactory $currencyFactory
     * @param CartExtensionFactory $cartExtensionFactory
     * @param CatalogRuleApplier $catalogRuleApplier
     */
    public function __construct(
        QuoteRepositoryInterface $quoteRepository,
        CartRepositoryInterface $cartRepository,
        ToQuoteCart $convertToQuoteCart,
        ToNativeCart $convertToNativeCart,
        ValidatorFactory $validatorFactory,
        TotalCalculator $totalCalculator,
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory,
        CartExtensionFactory $cartExtensionFactory,
        CatalogRuleApplier $catalogRuleApplier
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->cartRepository = $cartRepository;
        $this->convertToQuoteCart = $convertToQuoteCart;
        $this->convertToNativeCart = $convertToNativeCart;
        $this->validatorFactory = $validatorFactory;
        $this->totalCalculator = $totalCalculator;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->catalogRuleApplier = $catalogRuleApplier;
    }

    /**
     * Create quote
     *
     * @param Quote|CartInterface $cart
     * @param QuoteInterface $quote
     * @return QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createQuote($cart, $quote)
    {
        $quoteCart = $this->convertToQuoteCart->convert($cart);
        $quote
            ->setCartId($cart->getId())
            ->setCart($quoteCart)
            ->setStoreId($cart->getStoreId())
            ->setBaseQuoteTotal($this->totalCalculator->calculateBaseCatalogPriceTotal($cart))
            ->setQuoteTotal($this->totalCalculator->calculateCatalogPriceTotal($cart))
            ->setBaseQuoteTotalNegotiated($this->totalCalculator->calculateBaseNegotiatedQuoteTotal($cart))
            ->setQuoteTotalNegotiated($this->totalCalculator->calculateNegotiatedQuoteTotal($cart));

        $this->quoteRepository->save($quote);

        return $quote;
    }

    /**
     * Update quote
     *
     * @param Quote|CartInterface $cart
     * @param QuoteInterface $quote
     * @param bool $isSeller
     * @return QuoteInterface
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function updateQuote($cart, $quote, $isSeller = false)
    {
        $quoteCart = $this->convertToQuoteCart->convert($cart);
        $quote
            ->setCart($quoteCart)
            ->setCartId($cart->getId())
            ->setBaseQuoteTotal($this->totalCalculator->calculateBaseCatalogPriceTotal($cart))
            ->setQuoteTotal($this->totalCalculator->calculateCatalogPriceTotal($cart))
            ->setBaseQuoteTotalNegotiated($this->totalCalculator->calculateBaseNegotiatedQuoteTotal($cart))
            ->setQuoteTotalNegotiated($this->totalCalculator->calculateNegotiatedQuoteTotal($cart));

        $this->changeStatus($quote->getId(), $quote->getStatus(), false, $isSeller);

        $this->quoteRepository->save($quote);

        return $quote;
    }

    /**
     * Change quote items order
     *
     * @param int $quoteId
     * @param array $sortOrderMap
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function changeQuoteItemsOrder($quoteId, $sortOrderMap)
    {
        $items = [];
        $quote = $this->quoteRepository->get($quoteId);
        $childSortOrder = Converter::DEFAULT_SORT_ORDER;
        $cart = $this->cartRepository->get($quote->getCartId());

        foreach ($cart->getAllItems() as $item) {
            $sortOrder = array_search($item->getData(CartItemInterface::KEY_ITEM_ID), $sortOrderMap);
            $sortOrder = $sortOrder !== false ? $sortOrder : $childSortOrder;

            $item->setData(CartItemInterface::AW_CTQ_SORT_ORDER, $sortOrder);
            $items[$sortOrder] = $item;
            $childSortOrder++;
        }

        $cart->setItems($items);
        $this->cartRepository->save($cart);
        $this->updateQuote($cart, $quote);
    }

    /**
     * Retrieve cart by quote
     *
     * @param QuoteInterface|int $quote
     * @param bool $isNew
     * @param int|null $storeId
     * @return CartInterface|Quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Validate_Exception
     */
    public function getCartByQuote($quote, $isNew = false, $storeId = null)
    {
        if ($this->cartCached === null) {
            $quote = $quote instanceof QuoteInterface ? $quote : $this->quoteRepository->get($quote);
            if ($this->isOrderedQuote($quote)) {
                $cart = $this->getCartForOrderedQuote($quote, $isNew = false, $storeId = null);
            } else {
                $extensionAttributes = $this->cartExtensionFactory->create();
                try {
                    $oldCart = $this->cartRepository->get($quote->getCartId());
                    $extensionAttributes = $oldCart->getExtensionAttributes()
                        ? $oldCart->getExtensionAttributes()
                        : $this->cartExtensionFactory->create();
                    $isNew = false;
                } catch (NoSuchEntityException $e) {
                    $isNew = true;
                }

                $quote = $this->quoteRepository->get($quote->getId());
                $extensionAttributes->setAwCtqQuote($quote);
                $cart = $this->convertToNativeCart->convert($quote->getCart(), $isNew);
                $cart = $this->prepareCartByCurrencyCode($cart, $storeId);
                $cart->setExtensionAttributes($extensionAttributes);
                $cart->setTotalsCollectedFlag(false);
                $cart->setAwCtqIsNotRequireValidation(true);
                try {
                    if ($cart->getAwCtqQuoteIsChanged() && $quote->getStatus() == Status::PENDING_BUYER_REVIEW) {
                        $quote->setStatus(Status::PENDING_SELLER_REVIEW);
                        $quote->setNegotiatedDiscountValue(0);
                    }
                    $this->catalogRuleApplier->apply($cart);
                    $this->cartRepository->save($cart->collectTotals());
                    $this->updateQuote($cart, $quote);
                    foreach ($cart->getAllVisibleItems() as $item) {
                        $item->checkData();
                    }
                } catch (LocalizedException $e) {
                }
            }
            $this->cartCached = $cart;
        }

        return $this->cartCached;
    }

    /**
     * Check is ordered quote or not
     *
     * @param QuoteInterface $quote
     * @return bool
     */
    public function isOrderedQuote($quote)
    {
        return $quote->getStatus() == Status::ORDERED;
    }

    /**
     * Retrieve cart by quoteId
     *
     * @param int $quoteId
     * @return Quote|CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCartByQuoteId($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $cart = $this->convertToNativeCart->convert($quote->getCart());

        return $cart;
    }

    /**
     * Quote was purchased action
     *
     * @param Quote $cart
     * @param Order $order
     * @return QuoteInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function quotePurchased($cart, $order)
    {
        $quote = null;
        if ($cart->getExtensionAttributes()
            && $cart->getExtensionAttributes()->getAwCtqQuote()
        ) {
            $quoteId = $cart->getExtensionAttributes()->getAwCtqQuote()->getId();
            $quote = $this->changeStatus($quoteId, Status::ORDERED, false, !empty($cart->getAwCtqSellerId()));
            $quote->setOrderId($order->getEntityId());
            $this->updateQuote($cart, $quote);
        }
        return $quote;
    }

    /**
     * Change quote status
     *
     * @param int $quoteId
     * @param string $status
     * @param bool $toSave
     * @param bool $isSeller
     * @return QuoteInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Validate_Exception
     */
    public function changeStatus($quoteId, $status, $toSave = true, $isSeller = false)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $quote
            ->setStatus($status)
            ->setIsSeller($isSeller);

        /** @var Validator $validator */
        $validator = $this->validatorFactory->create(['event' => 'change_status', 'isSeller' => $isSeller]);
        if (!$validator->isValid($quote)) {
            throw new LocalizedException(__('We couldn\'t change quote status.'));
        }
        if ($toSave) {
            $this->quoteRepository->save($quote);
        }

        if (in_array($quote->getStatus(), [Status::DECLINED_BY_BUYER, Status::DECLINED_BY_SELLER])) {
            try {
                $cart = $this->cartRepository->getActive($quote->getCartId());
                $cart->setAwCtqIsNotRequireValidation(true);
                $cart->setIsActive(false);
                $this->cartRepository->save($cart);
            } catch (NoSuchEntityException $e) {
            }
        }

        return $quote;
    }

    /**
     * Validate cart items before buy
     *
     * @param CartInterface $cart
     * @return void
     * @throws LocalizedException
     */
    public function validateCartItemsBeforeBuy($cart)
    {
        foreach ($cart->getItemsCollection() as $item) {
            $item->checkData();
            if ($item->getHasError()) {
                throw new LocalizedException(__($item->getMessage()));
            }
        }
    }

    /**
     * Prepare cart by currency code
     *
     * @param CartInterface|Quote $cart
     * @param int|null $storeId
     * @return CartInterface|Quote
     * @throws NoSuchEntityException
     */
    private function prepareCartByCurrencyCode($cart, $storeId)
    {
        if ($storeId) {
            $store = $this->storeManager->getStore($storeId);
            if ($cart->getQuoteCurrencyCode() != $store->getCurrentCurrencyCode()) {
                $cart->setStore($store);
                $cart->setAwCtqIsNotRequireValidation(true);
            }
        }

        return $cart;
    }

    /**
     * Get cart for ordered quote
     *
     * @param QuoteInterface $quote
     * @param bool $isNew
     * @param int|null $storeId
     * @return CartInterface|Quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getCartForOrderedQuote($quote, $isNew = false, $storeId = null)
    {
        $cart = $this->convertToNativeCart->convert($quote->getCart(), $isNew);
        $extensionAttributes = $cart->getExtensionAttributes()
            ? $cart->getExtensionAttributes()
            : $this->cartExtensionFactory->create();
        $extensionAttributes->setAwCtqQuote($quote);
        $cart->setExtensionAttributes($extensionAttributes);
        /** @var Store $store */
        $store = $this->storeManager->getStore($cart->getStoreId());
        $currency = $this->currencyFactory->create()->load($cart->getQuoteCurrencyCode());
        $store->setCurrentCurrency($currency);
        foreach ($cart->getItemsCollection() as $item) {
            $item->setStore($store);
        }

        return $cart;
    }
}
