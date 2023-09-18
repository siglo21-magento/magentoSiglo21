<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Plugin\Model\Quote\QuoteRepository;

use Aheadworks\Ctq\Model\Cart\Purchase\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteRepository\SaveHandler;

/**
 * Class SaveHandlerPlugin
 * @package Aheadworks\Ctq\Plugin\Model\Quote\QuoteRepository
 */
class SaveHandlerPlugin
{
    /**
     * @var Validator
     */
    private $cartValidator;

    /**
     * @param Validator $cartValidator
     */
    public function __construct(
        Validator $cartValidator
    ) {
        $this->cartValidator = $cartValidator;
    }

    /**
     * Validate cart before save
     *
     * @param SaveHandler $subject
     * @param CartInterface $quote
     * @return void
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave($subject, CartInterface $quote)
    {
        if (!$this->cartValidator->isValid($quote) || $quote->getAwCtqThrowException()) {
            throw new LocalizedException(__('We can\'t update your shopping cart right now. Deal was settled.'));
        }
    }
}
