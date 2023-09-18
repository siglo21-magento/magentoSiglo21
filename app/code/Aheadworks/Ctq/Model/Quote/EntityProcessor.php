<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote;

use Aheadworks\Ctq\Model\Quote as QuoteModel;

/**
 * Class EntityProcessor
 * @package Aheadworks\Ctq\Model\Quote
 */
class EntityProcessor
{
    /**
     * @var array[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Prepare entity data before save
     *
     * @param QuoteModel $object
     * @return QuoteModel
     */
    public function prepareDataBeforeSave($object)
    {
        foreach ($this->processors as $processor) {
            $processor->beforeSave($object);
        }
        return $object;
    }

    /**
     * Prepare entity data after load
     *
     * @param QuoteModel $object
     * @return QuoteModel
     */
    public function prepareDataAfterLoad($object)
    {
        foreach ($this->processors as $processor) {
            $processor->afterLoad($object);
        }
        return $object;
    }
}
