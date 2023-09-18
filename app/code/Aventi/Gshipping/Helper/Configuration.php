<?php

namespace Aventi\Gshipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 *
 * @package Aventi\Gshipping\Helper
 */

class Configuration extends AbstractHelper
{

     /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

     /**
     * @param  @param \Aventi\Gshipping\etc\adminhtml\system
     */

      /**config */
    const ACTIVE = 'gshipping_config/shipping_settings/active';

    /**shipping carriers*/
    const ACTIVEPACKAGE = 'carriers/gshipping/activepackage';
    const UNITS = 'carriers/gshipping/units';
    const HIGH = 'carriers/gshipping/high';
    const WIDTH = 'carriers/gshipping/width';
    const LENGTH = 'carriers/gshipping/length';
    const WEIGHT = 'carriers/gshipping/weight';
    const CUBICACTIVE = 'carriers/gshipping/cubicactive';
    const CUBICCAPACITY = 'carriers/gshipping/cubiccapacity';

    /** Shipping free */ 
    const FREESHIPPING = 'carriers/gshipping/freeshipping';
    const SKUFREE = 'carriers/gshipping/skufree';
    const PRICEFREE = 'carriers/gshipping/pricefree';

    /** Payment terms -*/ 
    const LOWERFREIGHTPRICE = 'carriers/gshipping/lowerfreightprice';
    const RATEKG = 'carriers/gshipping/ratekg';
    const LOWWEIGHTPRICE = 'carriers/gshipping/lowweightprice';
    const FORMULA = 'carriers/gshipping/formula';

    /**Shipping terms */
    const NOCUSTOMERGROUP = 'carriers/gshipping/nocustomergroup';
    const NOCATEGORIES = 'carriers/gshipping/nocategories';
    const NOCITIES = 'carriers/gshipping/nocities';

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
       \Magento\Framework\App\Helper\Context $context,
       \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
       $this->resourceConnection = $resourceConnection;
    }



/*********************** Block shipping_settings *************************/

     /**
     * @return 1 active config
     * active
     */ 
    public function active($store=null)
    {
       return $this->scopeConfig->getValue(self::ACTIVE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

/*********************** Block carriers/gshipping ************************/

     /**
     * @return active_package 
     * Listed active_package code.
    */
    public function activePackage($store=null)
    {
       return $this->scopeConfig->getValue(self::ACTIVEPACKAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }


    /**
     * @return units
    */
    public function unitsDefault($store=null)
    {
       return $this->scopeConfig->getValue(self::UNITS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return high
    */
    public function highDefault($store=null)
    {
       return $this->scopeConfig->getValue(self::HIGH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

     /**
     * @return width
    */
    public function widthDefault($store=null)
    {
       return $this->scopeConfig->getValue(self::WIDTH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return length
    */
    public function lengthDefault($store=null)
    {
       return $this->scopeConfig->getValue(self::LENGTH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return weight
    */
    public function weightDefault($store=null)
    {
       return $this->scopeConfig->getValue(self::WEIGHT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
    * @return cubic active
    */
    public function cubicActive($store=null)
    {
       return $this->scopeConfig->getValue(self::CUBICACTIVE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
    * @return Cubic Capacity
    */
    public function cubicCapacity($store=null)
    {
       return $this->scopeConfig->getValue(self::CUBICCAPACITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /********************* Shipping free ***********************/
   
   /**
     * @return free shopping
    */
    public function freeShipping($store=null)
    {
       return $this->scopeConfig->getValue(self::FREESHIPPING, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

     /**
     * @return Price free
    */
    public function priceFree($store=null)
    {
       return $this->scopeConfig->getValue(self::PRICEFREE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
    * @return list SKUS free
    */
    public function skuFree($store=null)
    {
       return $this->scopeConfig->getValue(self::SKUFREE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /********************* Payment terms ***********************/

    /**
     * @return Lower freight price
    */

    public function lowerFreightPrice($store=null)
    {
       return $this->scopeConfig->getValue(self::LOWERFREIGHTPRICE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return Rate KG
    */
    public function ratekg($store=null)
    {
       return $this->scopeConfig->getValue(self::RATEKG, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return Weight Minor
    */
    public function lowweightprice($store=null)
    {
       return $this->scopeConfig->getValue(self::LOWWEIGHTPRICE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
    * @return formula
    */
    public function formula($store=null)
    {
       return $this->scopeConfig->getValue(self::FORMULA, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /********************* Shipping terms ***********************/

     /**
    * @return list Customer Group!
    */
    public function noCustomerGroup($store=null)
    {
       return $this->scopeConfig->getValue(self::NOCUSTOMERGROUP, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }


    /**
    * @return list Categories!
    */
    public function noCategories($store=null)
    {
       return $this->scopeConfig->getValue(self::NOCATEGORIES, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @return No cities!
    */
    public function noCities($store=null)
    {
       return $this->scopeConfig->getValue(self::NOCITIES, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

}
 