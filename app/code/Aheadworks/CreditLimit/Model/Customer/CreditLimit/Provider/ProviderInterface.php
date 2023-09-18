<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface ProviderInterface
 *
 * @package Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider
 */
interface ProviderInterface
{
    /**
     * Prepare credit limit data for specified customer
     *
     * @param int $customerId
     * @param int $websiteId
     * @return array
     * @throws LocalizedException
     */
    public function getData($customerId, $websiteId);
}
