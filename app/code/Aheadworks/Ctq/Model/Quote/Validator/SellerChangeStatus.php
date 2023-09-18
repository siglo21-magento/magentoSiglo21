<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Validator;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsPool;
use Aheadworks\Ctq\Model\Quote\ValidatorInterface;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class SellerChangeStatus
 * @package Aheadworks\Ctq\Model\Quote\Validator
 */
class SellerChangeStatus extends AbstractValidator implements ValidatorInterface
{
    /**
     * @var RestrictionsPool
     */
    private $statusRestrictionsPool;

    /**
     * @param RestrictionsPool $statusRestrictionsPool
     */
    public function __construct(RestrictionsPool $statusRestrictionsPool)
    {
        $this->statusRestrictionsPool = $statusRestrictionsPool;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($quote)
    {
        $oldStatus = $quote->getOrigData(QuoteInterface::STATUS);
        $newStatus = $quote->getStatus();

        if ($oldStatus != $newStatus) {
            $statusRestrictions = $this->statusRestrictionsPool->getRestrictions($oldStatus);
            if (!in_array($newStatus, $statusRestrictions->getNextAvailableStatuses())) {
                $this->_addMessages(['You can\'t change status.']);
            }
        }

        return empty($this->getMessages());
    }
}
