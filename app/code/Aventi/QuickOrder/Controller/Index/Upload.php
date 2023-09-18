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
class Upload extends \Magento\Framework\App\Action\Action
{

  protected $resultPageFactory;
  protected $jsonHelper;

  /**
   * @var \Magento\Framework\File\Csv
   */
  private $csv;

  /**
   * @var \Magento\Catalog\Api\ProductRepositoryInterface
   */
  private $productRepository;

  /**
   * @var \Magento\Framework\Pricing\Helper\Data
   */
  private $priceHelper;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  private $logger;

  /**
   * @var \Magento\Customer\Model\Session
   */
  private $session;

  /**
   * @var \Magento\Framework\Api\SearchCriteriaBuilder
   */
  private $searchCriteriaBuilder;

  /**
   * @var \Magento\Framework\Api\FilterBuilder
   */
  private $filterBuilder;

  /**
   * @var \Magento\Framework\Api\Search\FilterGroupBuilder
   */
  private $filterGroupBuilder;

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
    \Magento\Framework\File\Csv $csv,
    \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
    \Magento\Framework\Pricing\Helper\Data $priceHelper,
    \Psr\Log\LoggerInterface $logger,
    \Magento\Customer\Model\Session $session,
    \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
    \Magento\Framework\Api\FilterBuilder $filterBuilder,
    \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
    \Magento\Store\Model\StoreManagerInterface $storeManager,    
    \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface $getProductSalableQtyInterface,
    \Magento\InventorySalesApi\Api\StockResolverInterface $stockResolverInterface
  )
  {
    $this->resultPageFactory = $resultPageFactory;
    $this->jsonHelper = $jsonHelper;
    parent::__construct($context);
    $this->csv = $csv;
    $this->productRepository = $productRepository;
    $this->priceHelper = $priceHelper;
    $this->logger = $logger;
    $this->session = $session;

    $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    $this->filterBuilder = $filterBuilder;
    $this->filterGroupBuilder = $filterGroupBuilder;
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

      $response = [
        'status' => 400,
        'message' => __('The document is invalid')
      ];

      $files = $this->getRequest()->getFiles();
      $rows = [];
      if (!empty($files['document'])) {
        $files = $files['document'];

        if ($files['size'] <= 2194304) {
          if (in_array($files['type'], ['text/csv', 'application/vnd.ms-excel'])) {
            $this->csv->setDelimiter(';');
            $csvData = $this->csv->getData($files['tmp_name']);
            if (!empty($csvData)) {
              foreach ($csvData as $row => $data) {

                $data = explode(',', trim($data[0]));

                if ($row > 0) {

                  $r = $this->bind_product(null, null, $data[0]);

                  try {

                    if (!empty($data)) {

                      $filterGroup = $this->filterGroupBuilder;
                      $filterGroup->addFilter(
                        $this->filterBuilder
                          ->setField('ref')
                          ->setConditionType('like')
                          ->setValue('%' . $data[0] . '%')
                          ->create()
                      );

                      $searchCriteria = $this->searchCriteriaBuilder
                        ->setFilterGroups([$filterGroup->create()])
                        ->setPageSize(1)
                        ->create();

                      $filter = $this->productRepository->getList($searchCriteria);
                      $products = $filter->getItems();


                      /**
                       * Only add simple products a to cart
                       * isSalable() == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
                       */
                      foreach ($products as $product) {

                        if ($product->isSalable()) {

                          $websiteCode = $this->storeManager->getWebsite()->getCode();                    
                          try {
                              /*$productStock = $this->stockItemRepository->get($product->getId());
                              $stock = $productStock->getQty();*/
                              $stockId = $this->stockResolverInterface->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode)->getStockId();
                              $stock = $this->getProductSalableQtyInterface->execute($product->getSku(), $stockId);                        
                          }catch (\Exception $e){
                              $stock = 1;
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
                          if ($product->getTypeId() === 'simple' || $product->getTypeId() === 'virtual'  ) {
                            $finalPrice = ($this->session->isLoggedIn()) ? $product->getPriceInfo()->getPrice('final_price')->getValue() : 0;
                            
                            $r = $this->bind_product(
                              $product->getId(),
                              $product->getSku(),
                              $product->getData('ref'),
                              $product->getTypeId(),
                              [],
                              $product->getName(),
                              $product->getProductUrl(),
                              $this->priceHelper->currency($finalPrice, true, false),
                              $this->priceHelper->currency($finalPrice * (int)$data[1], true, false),
                              $finalPrice,
                              $data[1],
                              true,
                              $formatInventory
                              );


                          } else {
       
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

                                      $r = $this->bind_product(
                                      $product->getId(),
                                      $product->getSku(),
                                      $product->getData('ref'),
                                      $product->getTypeId(),
                                      $attributes,
                                      $product->getName(),
                                      $product->getProductUrl(),
                                      null,
                                      null,
                                      0,
                                      0,
                                      false,
                                      $formatInventory
                                    );                                   
              
                                  }   

                              }else{

                                $r = $this->bind_product(
                                  $product->getId(),
                                  $product->getSku(),
                                  $product->getData('ref'),
                                  $product->getTypeId() . ":invalid",
                                  [],
                                  __('Product') ." ".  $product->getTypeId()  . ": " . __('Reference needs to be loaded')  . ": " .  $product->getName(),
                                  $product->getProductUrl()
                                );
                              }


                          }

                        } else {
                          $r = $this->bind_product(
                            $product->getId(),
                            $product->getSku(),
                            $product->getData('ref'),
                            $product->getTypeId() . ":inactive",
                            [],
                            __('Status is inactive to')  . " " .  $product->getName(),
                            $product->getProductUrl()
                          );

                          //$this->logger->error(print_r($data, true));
                        }
                      }
                    }
                    $rows[] = $r;
                  } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $rows[] = $r;
                  }
                }
              }
              $response = [
                'status' => 200,
                'message' => $rows
              ];
            }
          } else {
            $response = [
              'status' => 400,
              'message' => __('The document is invalid')
            ];
          }
        } else {
          $response = [
            'status' => 400,
            'message' => __('The document is too big')
          ];
        }
      }
      return $this->jsonResponse($response);
    } catch (\Magento\Framework\Exception\LocalizedException $e) {
      return $this->jsonResponse([
        'status' => 400,
        'message' => __('The document is invalid')
      ]);
    } catch (\Exception $e) {
      $this->logger->critical($e);
      return $this->jsonResponse([
        'status' => 400,
        'message' => __('The document is invalid')
      ]);
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


  /**
   * Create Product
   *
   * @return object
   */
  public function bind_product($id=0, $sku=null, $ref=null, $type=null, $attributes=null, $name=null, $url='', $priceFormat=null, $totalFormat=null, $price=0, $qty=0, $status=false, $formatInventory = '')
  {
    $notPrice = $this->priceHelper->currency(0, true, false);
  
    $path = substr($url, strrpos($url, '/') + 1);    
    $subPath = 'aquickview/index/index/path/';    

    $r = [
      'id' => $id,
      'sku' => isset($sku) ? $sku : '-',
      'image' => '',
      'url' => $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . $subPath . $path,
      'name' => isset($name) ? $name : __('Not available'),
      'priceFormat' => isset($priceFormat) ? $priceFormat : $notPrice,
      'totalFormat' => isset($totalFormat) ? $totalFormat : $notPrice,
      'price' => $price,
      'qty' => $qty,
      'status' => $status,
      'type' => isset($type) ? $type : "",
      'attributes' => isset($attributes) ? $attributes : __('Not available'),
      'ref' => isset($ref) ? $ref : __('Not available'),
      'formatInventory' => $formatInventory
    ];

    return $r;
  }
}


