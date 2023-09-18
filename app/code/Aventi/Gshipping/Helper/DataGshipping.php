<?php
declare(strict_types=1);

namespace Aventi\Gshipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class DataGshipping extends AbstractHelper
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $_checkoutSession;

  
    /**
    * @param \Aventi\Gshipping\Helper\Configuration
    */
    private $_config;

    /**
     * @var \Magento\Framework\App\Http\Context 
     */
    private $_httpContext;

     /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
    * @var\Magento\Directory\Model\Currency
    */
    protected $_currency;

    /**
    * @param \Magento\Checkout\Model\Session $checkoutSession
    * @param \Magento\Framework\App\Helper\Context $context
    * @param \Aventi\Gshipping\Helper\Configuration $configuration
    * @param \Magento\Framework\App\Http\Context $httpContext
    * @param \Magento\Store\Model\StoreManagerInterface $storeManager
    * @param \Magento\Directory\Model\Currency $currency
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Helper\Context $context,
        \Aventi\Gshipping\Helper\Configuration $configuration,
        \Magento\Customer\Model\Session $httpContext,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currency
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_config = $configuration;
        $this->_httpContext = $httpContext;
        $this->_storeManager = $storeManager;
        $this->_currency = $currency;  
        parent::__construct($context);

    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

     /**
     * @return bool
     */
    public function isActive()
    {
        return $this->_config->active();
    }

    /**
     * @return bool
     */
    public function isActivePackage()
    {
        return $this->_config->activePackage();
    }

    /***
    *  Get width Default
    ** value
    ** 
    */
    public function getHighDefault()
    {
       return $this->_config->highDefault();
    }

    /***
    *  Get width Default
    ** value
    ** 
    */
    public function getWidthDefault()
    {
       return $this->_config->widthDefault();
    }

    /***
    *  Get length Default
    ** value
    ** 
    */
    public function getLengthDefault()
    {
       return $this->_config->lengthDefault();
    }

    /***
    *  Get weight Default
    ** value
    ** 
    */
    public function getWeightDefault()
    {
       return $this->_config->weightDefault();
    }

    /***
    *  Get units default
    ** select
    ** 
    */
    public function getUnitsDefault()
    {
       return $this->_config->unitsDefault();
    }


    /***
    *  Get Cubic Active
    ** select
    ** 
    */
    public function getCubicActive()
    {
       return $this->_config->cubicActive();
    }

     /***
    *  Get Cubic Capacity
    ** value
    ** 
    */
    public function getcubicCapacity()
    {
       return $this->_config->cubicCapacity();
    }

    
    /********************* Shipping free ***********************/

    /***
    *  Get free Shipping
    ** select
    ** 
    */
    public function getFreeShipping()
    {
       return $this->_config->freeShipping();
    }


    /***
    *  Get SKU Free
    ** value
    ** 
    */
    public function getSkuFree()
    {
       $skus = array();
       $skuFree = $this->_config->skuFree();
       if($skuFree){
          $skus = explode(",", $skuFree);
       }
       return $skus;
    }


    /***
    *  Get price free
    ** value
    ** 
    */
    public function getPriceFree()
    {
       return $this->_config->priceFree();
    }


    /********************* Payment terms ***********************/

    /***
    *  Get Price Minor
    ** value
    */
    public function getLowerFreightPrice()
    {  
         return $this->_config->lowerFreightPrice();
    }

    /***
    *  Get Rate KG
    ** value
    */
    public function getRatekg()
    {  
         return $this->_config->ratekg();
    }

    /***
    *  Get weight minor
    ** value
    */
    public function getLowweightprice()
    {  
         return $this->_config->lowweightprice();
    }

    /***
    *  Get Formula
    ** value
    */
    public function getFormula()
    {  
         return $this->_config->formula();
    }

    /********************* Shipping terms ***********************/

    /***
    *  Get Customer Group
    ** select configurations
    ** 
    */
    public function getNoCustomerGroup()
    {
       $customerGroup = array();
       $noCustomerGroup = $this->_config->noCustomerGroup();
       if($noCustomerGroup){
          $customerGroup = explode(",", $noCustomerGroup);
       }
       return $customerGroup;
    }

    /***
    *  Get Categories
    ** select configurations
    ** 
    */
    public function getNoCategories()
    {
       $categories = array();
       $noCategories = $this->_config->noCategories();
       if($noCategories){
          $categories = explode(",", $noCategories);
       }
       return $categories;
    }

    /***
    *  Get cities
    ** select configurations
    ** 
    */
    public function getNoCities()
    {  
       $cities = array();
       $noCities = $this->_config->noCities();
       if($noCities){
          $noCities = preg_replace('/\s+/', '', $noCities);
          $cities = explode(",", $noCities);
       }
       return $cities;
    }


     /***
    *  list products
    ** carts
    ** 
    */
    public function getProducts()
    {  
         return $this->_checkoutSession->getQuote()->getAllVisibleItems();
    }

      /***
    *  Get Items Count
    ** Value
    ** 
    */
    public function getItemsCount()
    {  
         return  $this->_checkoutSession->getQuote()->getItemsCount();;
    }


    /***
    *  Get subtotal
    ** Value
    ** 
    */
    public function getSubtotal()
    {  
         return $this->_checkoutSession->getQuote()->getSubtotal();
    }

    /***
    *  Get Grand Total
    ** Value
    ** 
    */
    public function getGrandTotal()
    {  
       
        return $this->_checkoutSession->getQuote()->getGrandTotal();
    }

    /***
    *  Customer Session
    ** id customer
    ** 
    */
    public function getCustomerSessionGroup()
    {  
         return $this->_httpContext->getCustomer()->getGroupId();
    }

    
     /**
     * Get current store currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }    
    
    
    /**
     * Get currency symbol for current locale and currency code
     *
     * @return string
     */    
    public function getCurrentCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }    







}

            