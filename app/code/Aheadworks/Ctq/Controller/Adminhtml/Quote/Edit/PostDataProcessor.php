<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit;

use Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor\Date as DateProcessor;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor\ProcessorInterface;

/**
 * Class PostDataProcessor
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit
 */
class PostDataProcessor
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $processors = [
        'date' => DateProcessor::class,
    ];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $processors
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $processors = []
    ) {
        $this->objectManager = $objectManager;
        $this->processors = array_merge($this->processors, $processors);
    }

    /**
     * Prepare post data for saving
     *
     * @param array $data
     * @return array
     * @throws \InvalidArgumentException
     */
    public function preparePostData($data)
    {
        foreach ($this->processors as $processorName => $class) {
            $processorInstance = $this->objectManager->create($class);
            if (!$processorInstance instanceof ProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf('Processor instance %s does not implement required interface.', $processorName)
                );
            }
            $data = $processorInstance->process($data);
        }
        return $data;
    }
}
