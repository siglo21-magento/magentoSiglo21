<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Cart;

use Aheadworks\Ctq\Api\Data\QuoteCartInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteCartInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Converter
 * @package Aheadworks\Ctq\Model\Quote\Cart
 */
class Converter
{
    /**
     * @var QuoteCartInterfaceFactory
     */
    private $quoteCartFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param QuoteCartInterfaceFactory $quoteCartFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        QuoteCartInterfaceFactory $quoteCartFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->quoteCartFactory = $quoteCartFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Convert object to array
     *
     * @param QuoteCartInterface $cart
     * @return array
     */
    public function toArray($cart)
    {
        // @todo implement toArray converter
        $array = $cart->getData();

        return $array;
    }

    /**
     * Convert array to object
     *
     * @param array $array
     * @return QuoteCartInterface
     */
    public function toDataModel($array)
    {
        $quoteCart = $this->quoteCartFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quoteCart,
            $array,
            QuoteCartInterface::class
        );

        return $quoteCart;
    }
}
