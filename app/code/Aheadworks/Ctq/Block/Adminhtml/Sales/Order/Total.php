<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Sales\Order;

use Magento\Framework\DataObject\Factory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;

/**
 * Class Total
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Sales\Order
 */
class Total extends Template
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @param Context $context
     * @param Factory $factory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $factory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->factory = $factory;
    }

    /**
     * Init totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $source = $this->getSource();
        if (!$source) {
            return $this;
        }

        if ($source->getBaseAwCtqAmount()) {
            $this->getParentBlock()->addTotal(
                $this->factory->create(
                    [
                        'code'   => 'aw_ctq_amount',
                        'strong' => false,
                        'label'  => __('Negotiated Discount'),
                        'value'  => $source->getAwCtqAmount(),
                        'base_value' => $source->getBaseAwCtqAmount()
                    ]
                )
            );
        }

        return $this;
    }

    /**
     * Retrieve totals source object
     *
     * @return Order|null
     */
    private function getSource()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock) {
            return $parentBlock->getSource();
        }
        return null;
    }
}
