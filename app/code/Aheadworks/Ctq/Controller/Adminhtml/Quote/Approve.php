<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Aheadworks\Ctq\Model\Source\Quote\Status;

/**
 * Class Approve
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class Approve extends Save
{
    /**
     * {@inheritdoc}
     */
    protected function prepareQuote($data)
    {
        $quoteObject = parent::prepareQuote($data);
        $quoteObject->setStatus(Status::PENDING_BUYER_REVIEW);

        return $quoteObject;
    }
}
