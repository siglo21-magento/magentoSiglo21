<?php

namespace Aventi\SAP\Helper;

use Aventi\SAP\Logger\Logger;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Data
 *
 * @package Aventi\SAP\Helper
 */
class Data extends AbstractHelper
{

    private $_token = null;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $_curl;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Configuration
     */
    private $_configHelper;

    /**
     * @var DateTime
     */
    private $_dateTime;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $_priceCurrency;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param Curl $curl
     * @param Logger $logger
     * @param Configuration $configHelper
     * @param DateTime $dateTime
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Aventi\SAP\Logger\Logger $logger,
        \Aventi\SAP\Helper\Configuration $configHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        parent::__construct($context);
        $this->_curl = $curl;
        $this->logger = $logger;
        $this->_configHelper = $configHelper;
        $this->_dateTime = $dateTime;
        $this->_priceCurrency = $priceCurrency;
    }

    /**
     * Returns generated token by SAP WS.
     * @return string|null
     */
    public function getToken(): ?string
    {
        if ($this->_token === null || $this->_token === '') {
            $this->generateToken();
        }
        return $this->_token;
    }

    public function setToken($token)
    {
        $this->_token = $token;
    }

    /**
     * Generates token from SAP WS to execute transactions.
     *
     * @return void
     */
    private function generateToken()
    {
        $mainUri = $this->_configHelper->getUrlWS();
        try {
            if ($mainUri) {
                $url = $mainUri . 'token';
                $params = [
                    'Username' => $this->_configHelper->getUser(),
                    'Password' => $this->_configHelper->getPassword(),
                    'grant_type' => 'password'
                ];
                $this->_curl->post($url, $params);
                $response = json_decode($this->_curl->getBody());

                if ($this->_curl->getStatus() != 400) {
                    $this->setToken($response->access_token);
                }
            } else {
                throw new LocalizedException(__("Main url webservice undefined."));
            }
        } catch (LocalizedException $exception) {
            $this->logger->error(__('An error has occurred: ' . $exception->getMessage()));
        }
    }

    public function postRecourse($typeUri, $params)
    {
        $mainUri = $this->_configHelper->getUrlWS();
        try {
            if ($mainUri) {
                $url = $this->formatUrl($mainUri, $typeUri);
                $headers = [
                    "Authorization" => "Bearer {$this->getToken()}"
                ];
                $this->_curl->setHeaders($headers);
                $this->_curl->post($url, $params);
                return [
                  'status' => $this->_curl->getStatus(),
                  'body' => $this->_curl->getBody()
              ];
            } else {
                throw new LocalizedException(__("Main url webservice undefined."));
            }
        } catch (LocalizedException $exception) {
            $this->logger->error(__('An error has occurred: ' . $exception->getMessage()));
        }
        return false;
    }

    /**
     *
     *
     * @param $typeUri
     * @param $start
     * @param $rows
     * @param $fast
     * @return mixed
     */
    public function getResource($typeUri, $start, $rows, $fast)
    {
        $mainUri = $this->_configHelper->getUrlWS();
        try {
            if ($mainUri) {
                $url = $this->formatUrl($mainUri, $typeUri, $start, $rows, $fast);
                $headers = [
                    "Content-Type" => "application/json",
                    "Authorization" => "Bearer {$this->getToken()}"
                ];
                $this->_curl->setHeaders($headers);
                $this->_curl->get($url);
                if ($this->_curl->getStatus() == 200) {
                    return $this->_curl->getBody();
                } else {
                    throw new LocalizedException(__("Error in connection to server: " . $this->_curl->getBody()));
                }
            } else {
                throw new LocalizedException(__("Main url webservice undefined."));
            }
        } catch (LocalizedException $e) {
            $this->logger->error(__('An error has occurred: ' . $e->getMessage()));
        }
        return false;
    }

    /**
     * Format SAP date.
     *
     * @param $date
     * @return string
     */
    public function formatDateSAP($date): string
    {
        $date = explode('T', $date);
        return  $date[0] . ' ' . str_replace(0, 1, $date[1]);
    }

    /**
     * Formats SAP Doc Total.
     * @param $total
     * @return string
     */
    public function formatDocTotal($total): string
    {
        return  $this->_priceCurrency->convertAndFormat(round($total, 2));
    }

    /**
     * @param $status
     * @param $canceled
     * @return string
     */
    public function formatStatus($status, $canceled): string
    {
        if ($canceled == 'Y') {
            return 'Cancelado';
        }
        $state = '';
        switch ($status) {
            case 'C':
                $state = 'Cerrado';
                break;
            case 'O':
            default:
                $state = 'Abierto';
                break;
        }

        return $state;
    }

    /**
     * Formats url depending $type
     * @param $mainUri
     * @param $type
     * @param $start
     * @param $rows
     * @param bool $fast
     * @return string
     * @throws LocalizedException
     */
    private function formatUrl($mainUri, $type, $start = null, $rows = null, bool $fast = false): string
    {
        switch ($type) {
            case 'price':
                $uri = $fast ? $this->_configHelper->getUrlPriceFast() : $this->_configHelper->getUrlPrice();
                $uri.= $start . "/" . $rows;
                break;
            case 'price_list':
                $uri = $this->_configHelper->getUrlPriceList();
                $uri.= $start . "/" . $rows;
                break;
            case 'product':
                $uri = $fast ? $this->_configHelper->getUrlProductsFast() : $this->_configHelper->getUrlProducts();
                $uri.= $start . "/" . $rows;
                break;
            case 'stock':
                $uri = $fast ? $this->_configHelper->getUrlStockFast() : $this->_configHelper->getUrlStock();
                $uri.= $start . "/" . $rows;
                break;
            case 'sales_stock':
                $uri = $this->_configHelper->getUrlSalesStock();
                $uri.= $start . "/" . $rows;
                break;
            case 'create_order':
                $uri = $this->_configHelper->getUrlCreateOrder();
                break;
            case 'update_order':
                $uri = $this->_configHelper->getUrlUpdateOrder();
                break;
            case 'draft_order':
                $date = date('Y-m-d', strtotime($this->_dateTime->date('Y-m-d') . ' - 1 days'));
                $uri = $this->_configHelper->getUrlDraftOrder();
                $uri.= $date . "/" . $start . "/" . $rows;
                break;
            case 'state_order':
                $date = date('Y-m-d', strtotime($this->_dateTime->date('Y-m-d') . ' - 360 days'));
                $uri = $this->_configHelper->getUrlStateOrder();
                $uri.= $date . "/" . $start . "/" . $rows;
                break;
            case 'sales_rule':
                $uri = $this->_configHelper->getUrlSales();
                $uri.= $start . "/" . $rows;
                break;
            case 'customer':
                $uri = $fast ? $this->_configHelper->getUrlCustomersFast() : $this->_configHelper->getUrlCustomers();
                $uri.= $start . "/" . $rows;
                break;
            case 'address':
                $uri = $fast ? $this->_configHelper->getUrlAddressesFast() : $this->_configHelper->getUrlAddresses();
                $uri.= $start . "/" . $rows;
                break;
            default:
                throw new LocalizedException(__("Option undefined"));
        }

        if ($uri === null || $uri === '') {
            throw new LocalizedException(__("The " . $type . " ws url is not set in admin configuration."));
        }

        return $mainUri . $uri;
    }

    /**
     * Send a custom request with custom parameters.
     * @param string $method
     * @param $param
     * @return false|string
     */
    public function getResourceCustom(string $method, $param)
    {
        $mainUri = $this->_configHelper->getUrlWS();
        try {
            if ($mainUri) {
                $url = $mainUri . $method . $param;
                $headers = [
                    "Content-Type" => "application/json",
                    "Authorization" => "Bearer {$this->getToken()}"
                ];
                $this->_curl->setHeaders($headers);
                $this->_curl->get($url);
                if ($this->_curl->getStatus() == 200) {
                    return $this->_curl->getBody();
                } else {
                    throw new LocalizedException(__("Error in connection to server: " . $this->_curl->getBody()));
                }
            } else {
                throw new LocalizedException(__("Main url webservice undefined."));
            }
        } catch (LocalizedException $e) {
            $this->logger->error(__('An error has occurred: ' . $e->getMessage()));
        }
        return false;
    }
}
