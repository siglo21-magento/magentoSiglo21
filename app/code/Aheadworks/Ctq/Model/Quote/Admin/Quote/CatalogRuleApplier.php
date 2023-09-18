<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Admin\Quote;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Framework\Registry;
use Magento\Framework\DataObjectFactory;

/**
 * Class CatalogRuleApplier
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Quote
 */
class CatalogRuleApplier
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @param Registry $coreRegistry
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        Registry $coreRegistry,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Apply catalog rule
     *
     * @param CartInterface|Quote $cart
     */
    public function apply($cart)
    {
        $ruleData = $this->dataObjectFactory->create();
        $ruleData->setData([
            'store_id' => $cart->getStoreId(),
            'website_id' => $cart->getStore()->getWebsiteId(),
            'customer_group_id' => $cart->getCustomerGroupId()
        ]);

        $this->coreRegistry->register('rule_data', $ruleData, true);
    }
}
