<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Ui\DataProvider\Company\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\Document as FrameworkDataProviderDocument;
use Magento\Framework\Api\AttributeInterface;

/**
 * Class Document
 *
 * @package Aheadworks\Ca\Ui\DataProvider\Company\Listing
 */
class Document extends FrameworkDataProviderDocument
{
    /**
     * Get an attribute value.
     *
     * @param string $attributeCode
     * @return AttributeInterface|null
     */
    public function getCustomAttribute($attributeCode)
    {
        /** @var AttributeInterface $attributeValue */
        $attributeValue = $this->attributeValueFactory->create();
        $attributeValue->setAttributeCode($attributeCode);
        $attributeValue->setValue($this->getAttributeValue($attributeCode));
        return $attributeValue;
    }

    /**
     * Retrieve attribute value in the string representation
     *
     * @param string $attributeCode
     * @return string
     */
    protected function getAttributeValue($attributeCode)
    {
        $attributeData = $this->getData($attributeCode);
        if (is_array($attributeData)) {
            $attributeValue = implode(', ', $attributeData);
        } else {
            $attributeValue = (string) $attributeData;
        }
        return $attributeValue;
    }
}
