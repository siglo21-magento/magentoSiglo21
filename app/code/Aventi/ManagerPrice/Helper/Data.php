<?php

namespace Aventi\ManagerPrice\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Hide price config path
     */
    const XML_CONFIG_HIDE_PRICE = 'price/available/hide_price';

    /**
     * Hide from groups config path
     */
    const XML_CONFIG_HIDE_PRICE_GROUPS = 'price/available/hide_price_groups';

    /**
     * Data constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct(
            $context
        );
    }

    /**
     * Retrieve value HIDE_PRICE from store configuration data
     * @return bool
     */
    public function _getConstHidePrice()
    {
        return (boolean) $this->scopeConfig->getValue(self::XML_CONFIG_HIDE_PRICE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve store configuration data
     *
     * @param   string $path
     * @return  string|null
     */
    public function _getConfig($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return customer state login
     *
     * @return bool
     */
    public function _getIsLogged()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $httpContext = $objectManager->get('Magento\Framework\App\Http\Context');

        return (boolean) $httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
}
