<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\EntityProcessor;

use Aheadworks\Ctq\Model\Quote as QuoteModel;
use Magento\Framework\Serialize\Serializer\Json;
use Aheadworks\Ctq\Model\Quote\Cart\Converter;

/**
 * Class CartSnapshot
 * @package Aheadworks\Ctq\Model\Quote\EntityProcessor
 */
class CartSnapshot
{
    /**
     * @var Converter
     */
    private $cartConverter;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param Converter $cartConverter
     * @param Json $serializer
     */
    public function __construct(
        Converter $cartConverter,
        Json $serializer
    ) {
        $this->cartConverter = $cartConverter;
        $this->serializer = $serializer;
    }

    /**
     * Convert cart snapshot data before save
     *
     * @param QuoteModel $object
     * @return QuoteModel
     */
    public function beforeSave($object)
    {
        if (is_object($object->getCart())) {
            $cartDataModel = $object->getCart();
            $cartArray = $this->cartConverter->toArray($cartDataModel);
            $object->setCart($this->serializer->serialize($cartArray));
        }

        return $object;
    }

    /**
     * Convert cart snapshot data after load
     *
     * @param QuoteModel $object
     * @return QuoteModel
     */
    public function afterLoad($object)
    {
        if ($object->getCart()) {
            $cartArray = $this->serializer->unserialize($object->getCart());
            $cartDataModel = $this->cartConverter->toDataModel($cartArray);
            $object->setCart($cartDataModel);
        }

        return $object;
    }
}
