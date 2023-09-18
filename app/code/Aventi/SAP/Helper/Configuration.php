<?php
/**
 * Copyright © Aventi SAS All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Configuration extends AbstractHelper
{
    /**
     * Definition of consts
     */
    const XML_PATH_PTK_SAP_PASSWORD_CUSTOMER = 'sap/setting/customer_password';
    const XML_PATH_WS_SAP_PATH = 'integration/settings/url';
    const XML_PATH_WS_SAP_USERNAME = 'integration/settings/user';
    const XML_PATH_WS_SAP_PASSWORD = 'integration/settings/password';
    const XML_PATH_WS_URL_PRODUCT = 'integration/ws_urls/ws_products_url';
    const XML_PATH_WS_URL_STOCK = 'integration/ws_urls/ws_stock_url';
    const XML_PATH_WS_URL_PRICE = 'integration/ws_urls/ws_price_url';
    const XML_PATH_WS_URL_CUSTOMERS = 'integration/ws_urls/ws_customers_url';
    const XML_PATH_WS_URL_CUSTOMERS_FAST = 'integration/ws_urls/ws_customers_fast_url';
    const XML_PATH_WS_URL_ADDRESS_FAST = 'integration/ws_urls/ws_addresses_fast_url';
    const XML_PATH_WS_URL_ADDRESS = 'integration/ws_urls/ws_addresses_url';
    const XML_PATH_WS_URL_STOCK_FAST = 'integration/ws_urls/ws_stock_fast_url';
    const XML_PATH_WS_URL_PRICE_FAST = 'integration/ws_urls/ws_price_fast_url';
    const XML_PATH_WS_URL_PRODUCT_FAST = 'integration/ws_urls/ws_products_fast_url';
    const XML_PATH_WS_URL_SALES_STOCK = 'integration/ws_urls/ws_sales_stock_url';
    const XML_PATH_WS_URL_PRICE_LIST = 'integration/ws_urls/ws_price_list_url';
    const XML_PATH_WS_URL_ORDERS_CREATE = 'integration/ws_urls/ws_create_orders_url';
    const XML_PATH_WS_URL_ORDERS_UPDATE = 'integration/ws_urls/ws_update_orders_url';
    const XML_PATH_WS_URL_DRAFT_ORDER = 'integration/ws_urls/ws_draft_orders_url';
    const XML_PATH_WS_URL_STATE_ORDER = 'integration/ws_urls/ws_status_orders_url';
    const XML_PATH_WS_URL_SALES = 'integration/ws_urls/ws_sales_url';

    const PATH_EMAIL = 'integration/customer/sendemail';
    const PATH_CC = 'integration/customer/copy';
    const PATH_SERIE = 'integration/document/serie';
    const XML_PATH_SAP_WHSCODE = 'integration/document/whscode';
    const XML_PATH_SAP_CN = 'integration/document/cardcode';
    const XML_PATH_SAP_SHIPPING = 'integration/document/shipping';
    const PATH_DOCDUEDATE = 'integration/document/docduedate';

    public function sendEmail($store = null)
    {
        return (int)$this->scopeConfig->getValue(self::PATH_EMAIL, ScopeInterface::SCOPE_STORE, $store);
    }

    public function copyEmail($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::PATH_CC, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Return the default serie to create document in SAP
     * @param null $store
     * @return string
     */
    public function getSerie($store = null)
    {
        return (string)$this->scopeConfig->getValue(self::PATH_SERIE, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Return the DocDueDate days to create document in SAP
     * @param null $store
     * @return int
     */
    public function getDocDueDate($store = null)
    {
        return (int)$this->scopeConfig->getValue(self::PATH_DOCDUEDATE, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlWS($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_SAP_PATH, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlProducts($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_PRODUCT, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlStock($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_STOCK, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlPrice($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_PRICE, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlCreateOrder($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_ORDERS_CREATE, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlUpdateOrder($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_ORDERS_UPDATE, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getWhsCode($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SAP_WHSCODE, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getShippingCode($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SAP_SHIPPING, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getCardCode($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SAP_CN, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlStockFast($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_STOCK_FAST, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUser($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_SAP_USERNAME, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getPassword($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_SAP_PASSWORD, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlPriceFast($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_PRICE_FAST, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlProductsFast($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_PRODUCT_FAST, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlSalesStock($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_SALES_STOCK, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlPriceList($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_PRICE_LIST, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlDraftOrder($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_DRAFT_ORDER, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlStateOrder($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_STATE_ORDER, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlSales($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_SALES, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlCustomers($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_CUSTOMERS, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlCustomersFast($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_CUSTOMERS_FAST, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlAddressesFast($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_ADDRESS_FAST, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getUrlAddresses($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WS_URL_ADDRESS, ScopeInterface::SCOPE_STORE, $store);
    }
}
