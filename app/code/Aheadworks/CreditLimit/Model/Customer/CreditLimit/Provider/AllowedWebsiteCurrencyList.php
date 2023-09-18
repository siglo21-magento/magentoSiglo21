<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider;

use Aheadworks\CreditLimit\Model\Website\CurrencyList;
use Magento\Framework\Locale\CurrencyInterface;

/**
 * Class CreditLimit
 *
 * @package Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider
 */
class AllowedWebsiteCurrencyList implements ProviderInterface
{
    /**
     * @var CurrencyList
     */
    private $currencyList;

    /**
     * @var CurrencyInterface
     */
    private $currency;

    /**
     * @param CurrencyList $currencyList
     * @param CurrencyInterface $currency
     */
    public function __construct(
        CurrencyList $currencyList,
        CurrencyInterface $currency
    ) {
        $this->currencyList = $currencyList;
        $this->currency = $currency;
    }

    /**
     * @inheritdoc
     */
    public function getData($customerId, $websiteId)
    {
        $options = [];
        $listOfCurrencyCodes = $this->currencyList->getAllowedCurrenciesForWebsite($websiteId);
        foreach ($listOfCurrencyCodes as $currencyCode) {
            $currency = $this->currency->getCurrency($currencyCode);
            $options[] = [
                'value' => $currencyCode,
                'label' => $currency->getName()
            ];
        }
        $data['allowedCurrencyList'] = $options;
        $data['baseCurrency'] = $this->currencyList->getBaseCurrencyForWebsite($websiteId);

        return $data;
    }
}
