<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote;

use Aheadworks\Ctq\Api\Data\QuoteActionInterface;
use Magento\Framework\DataObject;

/**
 * Class Action
 * @package Aheadworks\Ctq\Model\Quote
 */
class Action extends DataObject implements QuoteActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlPath()
    {
        return $this->getData(self::URL_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }
}
