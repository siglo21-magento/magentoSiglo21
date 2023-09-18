<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Customer\CreditLimit;

use Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider\ProviderInterface;

/**
 * Class DataProvider
 *
 * @package Aheadworks\CreditLimit\Model\Customer\CreditLimit
 */
class DataProvider implements ProviderInterface
{
    /**
     * Credit limit field data scope
     */
    const CREDIT_LIMIT_DATA_SCOPE = 'aw_credit_limit';

    /**
     * @var array
     */
    private $providers;

    /**
     * @param array $providers
     */
    public function __construct(
        array $providers = []
    ) {
        $this->providers = $providers;
    }

    /**
     * @inheritdoc
     */
    public function getData($customerId, $websiteId)
    {
        $data = [];
        foreach ($this->providers as $provider) {
            $data = array_merge($data, $provider->getData($customerId, $websiteId));
        }

        return [
            self::CREDIT_LIMIT_DATA_SCOPE => $data
        ];
    }
}
