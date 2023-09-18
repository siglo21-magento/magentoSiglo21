<?php

declare(strict_types=1);
namespace Aventi\Gshipping\Model\Carrier;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;

/**
 * Custom shipping model
 */
class Gshipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var
     */
    protected $_logger;

    /**
     * Code of the carrier
     *
     * @var string
     */
    protected $_settings = 'shipping_settings';
    protected $_code = 'gshipping';

    protected $_isFixed = true;
   
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $_checkoutSession;

    protected $_rateFactory;
    protected $_rateMethodFactory;
    protected $_trackFactory;

     /**
     * @var \Aventi\Gshipping\Helper\Data 
     */
    protected $_dataGshipping;

     /**
     * Rate request data
     *
     * @var RateRequest|null
     */
    protected $_request = null;

    /**
     * Rate result data
     *
     * @var Result|null
     */
    protected $_result = null;

     /**
     * Message Exceptions
     *
     * @var message
     */
    protected $_messageException;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Aventi\Gshipping\Helper\DataGshipping $cdata
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param array $data
     */

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackResultFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Aventi\Gshipping\Helper\DataGshipping $dataGshipping,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->_logger = $logger;
        $this->_rateFactory= $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_trackFactory = $trackResultFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_dataGshipping = $dataGshipping;
    }
    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {

        if (!$this->_dataGshipping->isActive()) {
            return false;
        }

        $checkShipping = $this->checkShipping($request);
        if (!$checkShipping->status) {
            return false;
        }

        $total = 0;
        $carrierTitle = $this->getConfigData('title');
        $methodTitle = $this->getConfigData('name');

        if ($checkShipping->status) {

            if(isset($checkShipping->value)) {
                $total = $checkShipping->value;
                $methodTitle = $methodTitle . '(' . $checkShipping->message . ')';
            }else{
                return false;
            }

        }

        $method =  $this->_rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($carrierTitle);
        $method->setMethod($this->_code);
        $method->setMethodTitle($methodTitle);
        $this->_result = $this->_rateFactory->create();
        $method->setPrice( $total );
        $method->setCost( $total );
        $this->_result->append($method);
        $this->_checkoutSession->unsPayment();
  
        return $this->getResult();
    }

    /**
     * getAllowedMethods
     *
     * @param array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

     /**
     * Get result of request
     *
     * @return Result|null
     */
    public function getResult()
    {
        if (!$this->_result) {
            $this->_result = $this->_trackFactory->create();
        }
        return $this->_result;
    }

    /**
     * Returns base currency rate.
     *
     * @param string
     * @return float
     * @return object
     * @param request
     * @throws LocalizedException
     */
    public function checkShipping($request)
    {

        $postalCode = $this->getPostalCode($request);
        $cities = $this->checkNoCities($request);
        $categories = $this->checkNoCategories();
        $customerGroup = $this->checkCustomerGroup();
        $priceFree = $this->checkPriceFree();

        if ($postalCode->zip === 0) {
            $this->_logger->info($postalCode->message);
            throw new LocalizedException($postalCode->message);
            return false;
        }

        if($cities->status === false) {
            $this->_logger->info($cities->message);
            throw new LocalizedException($cities->message);
            return $cities;
        }

        if($categories->status === false) {
            $this->_logger->info($categories->message);
            throw new LocalizedException($categories->message);
            return $categories;
        }

        if($customerGroup->status === false) {
            $this->_logger->info($customerGroup->message);
            throw new LocalizedException($customerGroup->message);
            return $customerGroup;
        }

        if($priceFree->status === true) {
            $this->_logger->info($priceFree->message);
            return $priceFree;
        }

        return $this->_requestQuote();
    }


    /**
     * Get result postal codde
     * @return ZipCode|0
     */
    public function getPostalCode($request)
    {
       $postalCode = $request->getDestPostcode();
       $response = (object)array('zip' => $postalCode, 'status' => true, 'message' => __('')); 
       if($postalCode == null){ 
         $response = (object)array(
            'zip' => 0, 
            'status' => false, 
            'message' => __('Debe seleccionar una ciudad o código postal válido.') 
        ); 
       }
       return $response;
    }

     /**
      * Get quote object associated with cart. By default it is current customer session quote
      *  US$3,45
      * @return \Magento\Quote\Model\Quote
      */
  
    protected function _requestQuote()
    {
        $response = (object)array('value' => 0, 'status' => false, 'message' => __('') );
        $order = [];
        $items = $this->_dataGshipping->getProducts();
        $unitsDefault = $this->_dataGshipping->getUnitsDefault();

        $highDefault = (float)$this->_dataGshipping->getHighDefault();
        $widthDefault = (float)$this->_dataGshipping->getWidthDefault();
        $lengthDefault = (float)$this->_dataGshipping->getLengthDefault();
        $weightDefault  = (float)$this->_dataGshipping->getWeightDefault();

        $height = 0;
        $length = 0;
        $weight = 0;
        $width = 0;

        $freeFlag = false;

        if(empty($items)){ 
            return $response;
        };

        if(!$this->_dataGshipping->isActivePackage()){

            foreach($items as $item) {

                $sku = $item->getSku();
                $skus[] = $sku;
                $productIds[] =  $item->getProductId();
                $namesProducts[] = $item->getName();
                $qty = $item->getQty();

                //get products
                $getHeight = $item->getHeight();
                $gettWidth = $item->getWidth();
                $getLength = $item->getLength();
                $getWeight = $item->getWeight();  
                
                // Set products
                $issetHeight = isset($getHeight) ? (float)$getHeight : $highDefault;
                $issetWidth =  isset($gettWidth) ? (float)$gettWidth :$widthDefault;
                $issetLength = isset($getLength) ? (float)$getLength : $lengthDefault;
                $issetWeight = isset($getWeight) ? (float)$getWeight : $weightDefault;

                $skuFree = $this->_requestSkusFree($sku);
                if($skuFree){
                    $freeFlag = true;
                }else{
                    // calc measures to products
                    $height += $issetHeight * $qty;
                    $width =  $issetWidth > $width ? $issetWidth : $width;
                    $length = $issetLength > $length ? $issetLength : $length;
                    $weight += $issetWeight * $qty; 
                }
            }
        }else{
            foreach($items as $item) {
                $sku = $item->getSku();
                $skus[] = $sku;
                $productIds[] =  $item->getProductId();
                $namesProducts[] = $item->getName();
                $skuFree = $this->_requestSkusFree($sku);
                 if($skuFree){
                    $freeFlag = true;
                }else{
                    $height = $highDefault;
                    $width = $widthDefault;
                    $length = $lengthDefault;
                    $weight = $weightDefault;
                } 
            }  
        }

        $productIds = implode(",",  $productIds);
        $skus = implode(",",  $skus);
        $namesProducts = implode(",",  $namesProducts);

        $order = (object)array(
            'ids' => $productIds,
            'skus' => $skus,
            'name_products' => $namesProducts,
            'height'  => $height,
            'width' => $width,
            'length' => $length,
            'weight' => isset($weight) ? $weight : $weightDefault,
            'units' => isset($unitsDefault) ? $unitsDefault : 1
        ); 

        if($skuFree && $weight == 0){
            $this->_logger->info(__("El envío gratis, porque todos las referencias tienen envío gratis"));
            return (object)array(
                'value' => 0, 
                'status' => true, 
                'message' => __('el envío es gratuito')
            );
        }
       return $this->_formula($order);
    }


      /**
     * Formula
     *@param Price Minor
     *@param Price Minor
     *@return value
     */
    protected function _formula($ord)
    {  
        $lfp = $this->_dataGshipping->getLowerFreightPrice();
        $lwp = $this->_dataGshipping->getLowweightprice();
        $rkg = $this->_dataGshipping->getRatekg();
        $search  = array('%LFP%', '%LWP%', '%RKG%', '%high%', '%width%', '%length%', '%weight%');
        $replace = array($lfp, $lwp, $rkg, $ord->height, $ord->width, $ord->length, $ord->weight);
        $formula = $this->_dataGshipping->getFormula();
        $setFormula = str_replace($search,$replace, $formula);
        $calculate = $this->calculate($setFormula, $lfp);
        return $calculate;
    }

    protected function calculate($formula, $lfp) {
        $response = (object)array('value' => 0, 'status' => false, 'message' => __(''));
        $calculate = 0;
        $formula = $this->setFormula($formula);

        
        if ($formula){
            $calculate = "return (".$formula.");";
            $calculate = eval($calculate);
            $response = (object)array(
                'value' => $calculate, 
                'status' => true, 
                'message' => __('valor estimado del envío'));
        }

        if ($calculate == null) {
            $response = (object)array(
            'value' => 0, 
            'status' => false, 
            'message' => __('Error no se puede hacer el calculo de la ecuación'));
        }

        if ($calculate <= $lfp) {
            $response = (object)array(
            'value' => $lfp, 
            'status' => true, 
            'message' => __('valor estimado del envío'));
        }

        return $response;
    }

    /**
    * Set Formula
    * @param formula
    * @return value
    */
    protected function setFormula($string) {

        // sanitize imput
        $str = preg_replace("/[^a-z0-9+\-.*\/()%]/","",$string);

        // convert alphabet to var
        $str = preg_replace("/([a-z])+/i", "\$$0", $str); 

        // convert percentages to decimal
        $str = preg_replace("/([+-])([0-9]{1})(%)/","*(1\$1.0\$2)",$str);
        $str = preg_replace("/([+-])([0-9]+)(%)/","*(1\$1.\$2)",$str);
        $str = preg_replace("/([0-9]{1})(%)/",".0\$1",$str);
        $str = preg_replace("/([0-9]+)(%)/",".\$1",$str);

        return $str;
    }

    /**
    * Check Weigh
    * @return weigh
    * @return volumetric
    */
    public function checkWeigh()
    {  
        $weigh = 0; 
        $volumetric = 0; 
        $cubicActive = $this->_dataGshipping->getCubicActive(); 
        $cubicCapacity = (float)$this->_dataGshipping->getcubicCapacity();
        $quote = $this->getQuoteData();
        $weigh = $quote->weight;
        if($cubicActive){
           $volumetric = $quote->length * $quote->width * $quote->height / $cubicCapacity;  
           if($volumetric > $weigh){  
            $weigh = $volume;
           }
        }
        return $weigh;
    }

    /**
     * Check Price Free
     * @return 1|0
     * @return object
     */
    public function checkPriceFree()
    {  
        $response = (object)array('status' => false, 'message' => __(''));
        $freeShipping = $this->freeShipping();
        $priceFree = (float)$this->_dataGshipping->getPriceFree(); 
        $subtotal = (float)$this->_dataGshipping->getSubtotal(); 
        $currency = $this->_dataGshipping->getCurrentCurrencySymbol();
        $currentCurrencyCode =  $this->_dataGshipping->getCurrentCurrencyCode();
        if($freeShipping){  
            if($subtotal >= $priceFree){
                $this->_logger->info(__('el envío es gratuito por compras mayores a') . " " . $currentCurrencyCode . $currency . $priceFree);
                $response = (object)array(
                    'value' => 0, 
                    'status' => true, 
                    'message' => __('el envío es gratuito por compras mayores a') . " " . $currentCurrencyCode . $currency . $priceFree 
                );
            }
        }    
        return $response;
    }

    /**
     * Check Skus Free
     * @param Skus Free
     * @return true:false
     */
    protected function _requestSkusFree($sku){  
        $skusFree = $this->checkSkusFree();
        $SkuFlag = false;
        foreach($skusFree as $value){
            if($value == $sku){
                $SkuFlag = true;
                break;
            }
        }

        return $SkuFlag;
    }

      /**
     * Check Skus Free
     * @return array
     */
    protected function checkSkusFree()
    {  
        $skus = array();
        $freeShipping = $this->freeShipping();
        $skusFree = $this->_dataGshipping->getSkuFree(); 
        if($freeShipping){  
            $products = $this->_dataGshipping->getProducts();
            foreach($skusFree as $sku){
                foreach($products as $product){
                    if($product->getSku() == $sku){
                        $skus[] = $sku;
                    }
                    
                }
            }
        }
        return $skus;
    }

    /**
     * Check Free Shipping
     * @return 0|1
     */
    protected function freeShipping()
    {  
        return $this->_dataGshipping->getFreeShipping();
    }
     
    /**
     * check Customer Group
     * @return false|true
    * @return object
     */
    public function checkCustomerGroup()
    {  
        $response = (object)array('status' => true, 'message' => __(''));
        $noCustomerGroups = $this->_dataGshipping->getNoCustomerGroup(); 
        $customerSessionGroup = $this->_dataGshipping->getCustomerSessionGroup(); 
        foreach($noCustomerGroups as $noCustomerGroup) {
            if($noCustomerGroup === $customerSessionGroup){
                $response = (object)array(
                    'status' => false,
                     'message' => __("Lo sentimos, no se permitern envíos para el grupo de usario") 
                 );
                break;
            }
        }
        return $response;
    }
    /**
     * get no categores
     * check no categories
     * @return false|true
    * @return object
     */
    public function checkNoCategories()
    {  
        $objectManager = ObjectManager::getInstance();
        $response = (object)array('status' => true, 'message' => __(''));
        $products = $this->_dataGshipping->getProducts();
        $noCategories = $this->_dataGshipping->getNoCategories();
        $flag = false;
        foreach($products as $product) {
            $categoryIds = $product->getProduct()->getCategoryIds();
            foreach($categoryIds as $categoryId) {
                foreach ($noCategories as $noCategory) {
                    $category = $objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
                    if($noCategory===$categoryId){
                        $response = (object)array(
                            'status' => false, 
                            'message' => __("Lo sentimos, no se permite envíos para el producto")
                            );
                        $flag=true;
                        break;
                    }
                }
            if($flag==true) break;   
            }
          if($flag==true) break;
        }
        return $response;
    }
     /**
     * get no citeies
     * check no cities shopping
     * @return false|true
     * @return object
     */
    public function checkNoCities($request)
    {
        //$destCity = $request->getDestCity();
        $postalCode = $this->getPostalCode($request);
        $response = (object)array('status' => true, 'message' => __(''));
        $getNoCities = $this->_dataGshipping->getNoCities();
        foreach ($getNoCities as $noCity) {
            if($noCity===$postalCode->zip){
                $response = (object)array(
                    'status' => false, 
                    'message' => __('No está permitido los envíos para la ciudad seleccioanada')
                );
                break;
            }
        }
        return $response; 
    }

}
