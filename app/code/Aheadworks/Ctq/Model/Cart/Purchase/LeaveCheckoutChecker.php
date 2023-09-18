<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Cart\Purchase;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;

/**
 * Class LeaveCheckoutChecker
 * @package Aheadworks\Ctq\Model\Cart\Purchase
 */
class LeaveCheckoutChecker
{
    /**
     * @var array
     */
    private $allow = [];

    /**
     * @var array
     */
    private $disallow = [];

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param array $allow
     * @param array $disallow
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        array $allow = [],
        array $disallow = []
    ) {
        $this->cartRepository = $cartRepository;
        $this->allow = $allow;
        $this->disallow = $disallow;
    }

    /**
     * Check if customer leave checkout
     *
     * @param Quote $cart
     * @param string|null $module
     * @param string|null $controller
     * @param string|null $action
     * @return bool
     */
    public function isLeave($cart, $module, $controller, $action)
    {
        $result = false;

        if ($cart->getIsActive()
            && $cart->getExtensionAttributes()
            && $cart->getExtensionAttributes()->getAwCtqQuote()
        ) {
            $result = true;

            if ($this->isInAllowList($module, $controller, $action)) {
                $result = false;
            }
            if ($this->isInDisallowList($module, $controller, $action)) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * @param string|null $module
     * @param string|null $controller
     * @param string|null $action
     * @return bool
     */
    private function isInAllowList($module, $controller, $action)
    {
        foreach ($this->allow as $exclusion) {
            if ($module == $exclusion['module']
                && ($controller == $exclusion['controller'] || '*' == $exclusion['controller'])
                && ($action == $exclusion['action'] || '*' == $exclusion['action'])
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string|null $module
     * @param string|null $controller
     * @param string|null $action
     * @return bool
     */
    private function isInDisallowList($module, $controller, $action)
    {
        foreach ($this->disallow as $exclusion) {
            if ($module == $exclusion['module']
                && ($controller == $exclusion['controller'] || '*' == $exclusion['controller'])
                && ($action == $exclusion['action'] || '*' == $exclusion['action'])
            ) {
                return true;
            }
        }

        return false;
    }
}
