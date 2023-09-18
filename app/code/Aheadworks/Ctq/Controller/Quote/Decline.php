<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Quote;

use Aheadworks\Ctq\Model\Source\Quote\Status;

/**
 * Class Decline
 * @package Aheadworks\Ctq\Controller\Quote
 */
class Decline extends ChangeStatus
{
    /**
     * {@inheritdoc}
     */
    protected function performSave($status)
    {
        $status = Status::DECLINED_BY_BUYER;
        parent::performSave($status);
    }
}
