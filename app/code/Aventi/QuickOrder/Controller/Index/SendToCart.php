<?php
/**
 * Quick order by parte equipos
 * Copyright (C) 2018  
 * 
 * This file is part of Aventi/QuickOrder.
 * 
 * Aventi/QuickOrder is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aventi\QuickOrder\Controller\Index;

use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
class SendToCart extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cart;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $productRepository;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Aventi\Quote\Helper\Product
     */
    private $productHelper;
    /**
     * @var \Magento\Catalog\Model\Session
     */
    private $catalogSession;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManagerInterface;
    /**
     * @var \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface
     */
    private $getProductSalableQtyInterface;
    /**      
     * @var \Magento\InventorySalesApi\Api\StockResolverInterface
     */
    private $stockResolverInterface;
    /**     
     * @var \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface
     */
    private $getStockItemConfigurationInterface;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function  __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Psr\Log\LoggerInterface $logger,
        \Aventi\QuickOrder\Helper\Product $productHelper,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface $getProductSalableQtyInterface,
        \Magento\InventorySalesApi\Api\StockResolverInterface $stockResolverInterface,
        \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface $getStockItemConfigurationInterface
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->productHelper = $productHelper;
        $this->catalogSession = $catalogSession;
        $this->productFactory = $productFactory;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->getProductSalableQtyInterface = $getProductSalableQtyInterface;
        $this->stockResolverInterface = $stockResolverInterface;
        $this->getStockItemConfigurationInterface = $getStockItemConfigurationInterface;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {

            $request = file_get_contents('php://input');
            $messages = $itemInCart = [];

            $itemCart = $this->cart->getQuote()->getItemsCollection();
            foreach ($itemCart as $item) {
               $itemInCart[]= $item->getProductId();
            }


            $status = false;

            if(!empty($request)){
                $products = json_decode($request,true);
                
                $state = $this->validateStock($products);                
                $status = $state['status'];
                if($status){
                    $this->catalogSession->setProductQuote($products);     
                    foreach ($products['products'] as $product){
                        
                        try {
                            $params = array(
                                'form_key' => $this->formKey->getFormKey(),
                                'product' => $product['id'],
                                'qty' => (int)$product['qty']
                            );               
                            $_product = $this->productFactory->create()->setStoreId($this->storeManagerInterface->getStore()->getId())->load($params['product']);                                 
                            //$_product = $this->productRepository->getById($product['id']);                        
                            $validateStock = $_product;
                            $options = [];                                                          
                            if(isset($product['type']) && $product['type'] == 'configurable'){
                                $childId = 0;                      
                                if(array_key_exists('selected', $product)){
                                    foreach ($product['selected'] as $key => $value) {
                                        
                                        foreach ($product['attributes'] as $k => $v) {
                                            if($value['name'] === "super_attribute[". $v['id'] ."]"){
                                                $options[$v['id']] = $value['value'];
                                            }
                                            else if($value['name'] === "selected_configurable_option"){
                                                $childId = $value['value'];
                                            }                                           
                                        }
                                    }
                                }     
                                $validateStock = $this->productRepository->getById($childId);                                                                           
                            }                                                                                                
                            $params['super_attribute'] = $options;  
                            $this->cart->addProduct($_product, $params);
                                                    
                        }catch (\Exception $e){
                            $this->logger->error($e->getMessage());
                            $messages[] = $_product->getName().'  <span>'. _($e->getMessage()).'</span>';
                        }                    
                    }
                    $this->cart->save();                    
                }else{                    
                    $messages = $state['message'];
                }                                        
            }
            return $this->jsonResponse(
                [
                    'message' => $messages,
                    'classCSS' => 'success',
                    'items' =>   /*$this->cart->getItemsCount()*/ count($products),
                    'status' => $status
                ]

                );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {

            $messages[] = $e->getMessage();
            return $this->jsonResponse(
                [
                    'message' => $messages,
                    'classCSS' => 'error',
                    'items' =>  $this->cart->getItemsCount(),
                    'status' => false
                ]);
        } catch (\Exception $e) {
            $messages[] = 'Error';
            $this->logger->critical($e);
            return $this->jsonResponse(
                [
                    'message' => $messages,
                    'classCSS' => 'error',
                    'items' =>  $this->cart->getItemsCount(),
                    'status' => false
                ]);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }

    public function validateStock($products){

        $status = false;
        $count = 0;
        $messages = [];
        foreach ($products['products'] as $product){
            
            try {
                $params = array(
                    'form_key' => $this->formKey->getFormKey(),
                    'product' => $product['id'],
                    'qty' => (int)$product['qty']
                );               
                $_product = $this->productFactory->create()->setStoreId($this->storeManagerInterface->getStore()->getId())->load($params['product']);                                 
                
                $validateStock = $_product;
                                                                    
                if(isset($product['type']) && $product['type'] == 'configurable'){
                    $childId = 0;                      
                    if(array_key_exists('selected', $product)){
                        foreach ($product['selected'] as $key => $value) {
                            
                            foreach ($product['attributes'] as $k => $v) {
                                if($value['name'] === "selected_configurable_option"){
                                    $childId = $value['value'];
                                }                                           
                            }
                        }
                    }     
                    $validateStock = $this->productRepository->getById($childId);                                                                           
                }                                                                                                
                        
                $websiteCode = $this->storeManagerInterface->getWebsite()->getCode();
                $stockId = $this->stockResolverInterface->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode)->getStockId();
                $stockConfig = $this->getStockItemConfigurationInterface->execute($validateStock->getSku(), $stockId);                        
                $stock = $this->getProductSalableQtyInterface->execute($validateStock->getSku(), $stockId);           
                
                if($stockConfig->getBackorders() != 0){
                    if($stock <= 0 || (int)$product['qty'] > $stock) {
                        $messages[] = $_product->getName().'  <span>'. __("Have not available units").'</span>';
                        $count++;                    
                    }
                }                       
                                        
            }catch (\Exception $e){
                $this->logger->error($e->getMessage());
                $messages[] = $_product->getName().'  <span>'. __($e->getMessage()).'</span>';
                $count++;
            }                    
        } 

        if($count == 0){
            $status = true;
        }

        return [
            "status" => $status,
            "message" => $messages
        ];

    }
}
