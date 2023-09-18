<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Service;

use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Aheadworks\Ctq\Api\Data\CartInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Config;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsPool;
use Aheadworks\Ctq\Model\Source\Quote\Action;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class BuyerPermissionService
 *
 * @package Aheadworks\Ctq\Model\Service
 */
class BuyerPermissionService implements BuyerPermissionManagementInterface
{
    /**
     * @var RestrictionsPool
     */
    private $statusRestrictionsPool;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param RestrictionsPool $statusRestrictionsPool
     * @param QuoteRepositoryInterface $quoteRepository
     * @param CartRepositoryInterface $cartRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        RestrictionsPool $statusRestrictionsPool,
        QuoteRepositoryInterface $quoteRepository,
        CartRepositoryInterface $cartRepository,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->statusRestrictionsPool = $statusRestrictionsPool;
        $this->quoteRepository = $quoteRepository;
        $this->cartRepository = $cartRepository;
        $this->config = $config;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function canBuyQuote($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $statusRestrictions = $this->statusRestrictionsPool->getRestrictions($quote->getStatus());

        return in_array(Status::ORDERED, $statusRestrictions->getNextAvailableStatuses());
    }

    /**
     * {@inheritdoc}
     */
    public function canRequestQuote($cartId)
    {
        try {
            $cart = $this->cartRepository->getActive($cartId);
            $result = !empty($cart->getCustomerId())
                && $this->isAllowedForCurrentCustomerGroup($cart->getCustomer()->getGroupId(), $cart->getStore());
        } catch (\Exception $e) {
            $result = false;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function canRequestQuoteList($cartId)
    {
        try {
            $cart = $this->cartRepository->getActive($cartId);
            $customerId = $cart->getData(CartInterface::AW_CTQ_QUOTE_LIST_CUSTOMER_ID);
            $customer = $this->customerRepository->getById($customerId);
            $result = $this->config->isQuoteListEnabled($cart->getStore()->getWebsiteId())
                && $this->isAllowedForCurrentCustomerGroup($customer->getGroupId(), $cart->getStore());
        } catch (\Exception $e) {
            $result = false;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowQuotesForCustomer($customerId, $storeId)
    {
        try {
            /** @var CustomerInterface $customer */
            $customer = $this->customerRepository->getById($customerId);
            $store = $this->storeManager->getStore($storeId);
            $result = $this->isAllowedForCurrentCustomerGroup($customer->getGroupId(), $store);
        } catch (\Exception $e) {
            $result = false;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowQuoteList($customerGroupId, $storeId)
    {
        try {
            $store = $this->storeManager->getStore($storeId);
            $result = $this->config->isQuoteListEnabled($store->getWebsiteId())
                && $this->isAllowedForCurrentCustomerGroup($customerGroupId, $store);
        } catch (\Exception $e) {
            $result = false;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowQuoteUpdate($websiteId = null)
    {
        return !$this->config->isAutoAcceptEnabled($websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowItemsSorting($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $restriction = $this->statusRestrictionsPool->getRestrictions($quote->getStatus());
        $availableActions = $restriction->getBuyerAvailableActions();

        return in_array(Action::SORT, $availableActions);
    }

    /**
     * Is allowed for current customer group
     *
     * @param int $customerGroupId
     * @param StoreInterface $store
     * @return bool
     */
    private function isAllowedForCurrentCustomerGroup($customerGroupId, StoreInterface $store)
    {
        $websiteId = $store->getWebsiteId();
        return in_array(
            $customerGroupId,
            $this->config->getCustomerGroupsToRequestQuote($websiteId)
        );
    }
}
