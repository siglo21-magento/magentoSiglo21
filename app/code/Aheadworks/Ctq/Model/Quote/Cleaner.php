<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote;

use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Model\Cart\Purchase\LeaveCheckoutChecker;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

/**
 * Class Cleaner
 * @package Aheadworks\Ctq\Model\Quote
 */
class Cleaner
{
    /**
     * Cookie name
     */
    const COOKIE_NAME = 'section_data_ids';

    /**
     * Cart section name
     */
    const CART_SECTION_NAME = 'cart';

    /**
     * @var LeaveCheckoutChecker
     */
    private $leaveCheckoutChecker;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var BuyerQuoteManagementInterface
     */
    private $buyerQuoteManagement;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var array
     */
    private $clearedCartIds = [];

    /**
     * @param Session $checkoutSession
     * @param LeaveCheckoutChecker $checker
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        Session $checkoutSession,
        LeaveCheckoutChecker $checker,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->leaveCheckoutChecker = $checker;
        $this->buyerQuoteManagement = $buyerQuoteManagement;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * Clear cart if customer leave quote checkout
     *
     * @param RequestInterface $request
     * @return void|null
     * @throws LocalizedException
     */
    public function clear($request)
    {
        if ($request->isAjax()) {
            return;
        }

        $cart = $this->checkoutSession->getQuote();
        $isLeave = $this->leaveCheckoutChecker->isLeave(
            $cart,
            $request->getModuleName(),
            $request->getControllerName(),
            $request->getActionName()
        );

        if (!in_array($cart->getId(), $this->clearedCartIds) && $isLeave) {
            $this->buyerQuoteManagement->clearCart($cart);
            $this->checkoutSession->clearQuote();
            $this->modifyCookie();
            $this->clearedCartIds[] = $cart->getId();
        }

        return null;
    }

    /**
     * Modify cookie
     */
    private function modifyCookie()
    {
        try {
            $cookieValue = \Zend_Json::decode((string)$this->cookieManager->getCookie(self::COOKIE_NAME));
            $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                ->setDuration(86400)
                ->setPath('/');

            $cookieValue[self::CART_SECTION_NAME] = 0;
            $this->cookieManager->setPublicCookie(
                self::COOKIE_NAME,
                \Zend_Json::encode($cookieValue),
                $cookieMetadata
            );
        } catch (\Exception $e) {
        }
    }
}
