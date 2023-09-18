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

class Search extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;
    /**
     * @var \Aventi\Template\Helper\Customer
     */
    private $customer;
    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    private $stockState;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    private $stockItemRepository;

    private $productHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    private $getProductSalableQtyInterface;

    private $stockResolverInterface;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Aventi\QuickOrder\Helper\Customer $customer,
        \Psr\Log\LoggerInterface $logger,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface $getProductSalableQtyInterface,
        \Magento\InventorySalesApi\Api\StockResolverInterface $stockResolverInterface
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->priceHelper = $priceHelper;
        $this->customer = $customer;
        $this->logger = $logger;
        $this->stockItemRepository = $stockItemRepository;
        $this->productHelper = $productHelper;
        $this->storeManager = $storeManager;
        $this->getProductSalableQtyInterface = $getProductSalableQtyInterface;
        $this->stockResolverInterface = $stockResolverInterface;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {

            $re = '/[^a-zA-Z0-9-_\. ]/m';
            //$data = $this->request->getParam('search');
            $data = $this->request->getParam('sku');
            $data = preg_replace($re, '', $data);
            $r = [
                'id' => 0,
                'sku' => trim($data),
                'url' => '',
                'image' => '',
                'name' => __('Not available'),
                'priceFormat' => $this->priceHelper->currency(0, true, false),
                'totalFormat' => $this->priceHelper->currency(0, true, false),
                'price' => 0,
                'qty' => 1,
                'status' => false,
                'type' => '',
                'attributes' => [],
                'ref' => ''
            ];
            
            if ($product = $this->productRepository->get(trim($data))) {
                if (
                    $product->getStatus() == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) {
                    $finalPrice = ($this->customer->isLoggedIn()) ?  $product->getPriceInfo()->getPrice('final_price')->getValue() : 0;
                    $websiteCode = $this->storeManager->getWebsite()->getCode();                    
                    try {
                        /*$productStock = $this->stockItemRepository->get($product->getId());
                        $stock = $productStock->getQty();*/
                        $stockId = $this->stockResolverInterface->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode)->getStockId();
                        $stock = $this->getProductSalableQtyInterface->execute($product->getSku(), $stockId);                        
                    }catch (\Exception $e){
                        $stock = 1;
                    }   
                    $attributes = [];
                    $childs = [];
                    if($product->getTypeId() === 'configurable'){
                        foreach ($product->getTypeInstance(true)->getConfigurableAttributesAsArray($product) as $option) {
                        
                        
                            $attr = [];                            
                            foreach ($option['options'] as $value) {
                             
                                $attr[] = [                                                                
                                    'value' => $value['value'],
                                    'label' => $value['label'],                                
                                ]; 
    
                            }                                                      
                            $attributes[] = [
                                'id' => $option['attribute_id'],
                                'code' => $option['attribute_code'],
                                'label' => $option['frontend_label'],
                                'options' => $attr
                            ];                                      
    
                        }   
                        /*if(!empty($attributes)){
                            foreach ($product->getTypeInstance(true)->getUsedProducts($product) as $key => $child){                            
                                var_dump($child->getData($attributes[$key]['code']));
                                foreach ($variable as $key => $value) {
                                    
                                }
                                
                            }
                        }                                             
                        die();*/
                    } 
                    $formatInventory = '<div class="inventory-button">
                                            <a href="#" id="btn-show-inventory" data-sku="'.$product->getSku().'" class="btn-show-inventory"><svg width="17" height="15" viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.7143 1H1V10.6429H11.7143V1Z" stroke="#2F3E47" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M11.7139 4.21423H14.1628L15.9996 6.62495V10.6428H11.7139V4.21423Z" stroke="#2F3E47" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M3.67843 13.8571C4.56603 13.8571 5.28557 13.1376 5.28557 12.25C5.28557 11.3624 4.56603 10.6428 3.67843 10.6428C2.79083 10.6428 2.07129 11.3624 2.07129 12.25C2.07129 13.1376 2.79083 13.8571 3.67843 13.8571Z" stroke="#2F3E47" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M13.321 13.8571C14.2086 13.8571 14.9282 13.1376 14.9282 12.25C14.9282 11.3624 14.2086 10.6428 13.321 10.6428C12.4334 10.6428 11.7139 11.3624 11.7139 12.25C11.7139 13.1376 12.4334 13.8571 13.321 13.8571Z" stroke="#2F3E47" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            </a>
                                        </div>';      
                    $listMaterial = ($product->getCustomAttribute('list_material')) ? $product->getCustomAttribute('list_material')->getValue() : 0;                    
                    if($listMaterial){
                        $formatInventory = '<span class="inventory-lm">'. $stock .'</span>';
                    }            
                    $path = $product->getProductUrl();    
                    $path = substr($path, strrpos($path, '/') + 1);    
                    $subPath = 'aquickview/index/index/path/';                             
                    $r = [
                        'id' => $product->getId(),
                        'sku' => trim($data),
                        'image' => $this->productHelper->getThumbnailUrl($product),
                        'url' => $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . $subPath . $path,
                        'name' => $product->getName(), //($stock <= 0) ? 'Disponible para importación, el precio puede variar' :  $product->getName(),
                        'priceFormat' =>  ($stock <= 0) ? 0 : $this->priceHelper->currency($finalPrice, true, false),
                        'totalFormat' => ($stock <= 0) ? 0 : $this->priceHelper->currency($finalPrice , true, false),
                        'price' => $finalPrice,
                        'qty' => 1,
                        'status' => ($stock <= 0) ? false : true,
                        'type' => $product->getTypeId(),
                        'attributes' => $attributes,
                        'ref'=> $product->getData('ref'),
                        'formatInventory' => $formatInventory
                    ];                    
                }
            }
            return $this->jsonResponse($response = [
                'status' => 200,
                'message' => [$r]
            ]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
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
}
