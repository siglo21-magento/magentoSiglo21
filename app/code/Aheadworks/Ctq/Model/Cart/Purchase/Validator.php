<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Cart\Purchase;

use Magento\Framework\Validator\AbstractValidator;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

/**
 * Class Validator
 * @package Aheadworks\Ctq\Model\Cart\Purchase
 */
class Validator extends AbstractValidator
{
    /**
     * @var AbstractValidator[]
     */
    private $validators;

    /**
     * @param AbstractValidator[] $validators
     */
    public function __construct(array $validators = [])
    {
        $this->validators = $validators;
    }

    /**
     * Validate cart
     *
     * @param CartInterface|Quote $cart
     * @return bool
     */
    public function isValid($cart)
    {
        if ($cart->getExtensionAttributes()
            && $cart->getExtensionAttributes()->getAwCtqQuote()
            && (!$cart instanceof Quote
                || ($cart instanceof Quote && !$cart->getAwCtqIsNotRequireValidation())
            )
        ) {
            foreach ($this->validators as $validator) {
                if (!$validator->isValid($cart)) {
                    $this->_addMessages($validator->getMessages());
                }
            }
        }

        return empty($this->getMessages());
    }
}
