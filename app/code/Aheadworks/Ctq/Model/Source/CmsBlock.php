<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Source;

use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CmsBlock
 * @package Aheadworks\Ctq\Model\Source
 */
class CmsBlock implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $blockCollectionFactory;

    /**
     * @var array
     */
    private $options;

    /**
     * @param CollectionFactory $blockCollectionFactory
     */
    public function __construct(CollectionFactory $blockCollectionFactory)
    {
        $this->blockCollectionFactory = $blockCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $options = $this->blockCollectionFactory->create()->toOptionArray();
            array_unshift(
                $options,
                [
                    'value' => '',
                    'label' => __('Please select a static block')
                ]
            );

            $this->options = $options;
        }

        return $this->options;
    }
}
