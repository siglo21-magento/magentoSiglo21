<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\ShowInventory\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    private $logger;    

    private $getSourceItemsBySkuInterface;

    private $sourceRepositoryInterface;

    private $sourceCollectionFactory;

    private $productRepository;

    private $excludeSource;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItemsBySkuInterface,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepositoryInterface,
        \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory $sourceCollectionFactory,              
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository   
    ) {
        $this->logger = $logger;        
        $this->getSourceItemsBySkuInterface = $getSourceItemsBySkuInterface;
        $this->sourceRepositoryInterface = $sourceRepositoryInterface;
        $this->sourceCollectionFactory = $sourceCollectionFactory;
        $this->productRepository = $productRepository;
        $this->excludeSource = [
            'default',
            'CDLM'
        ];
        parent::__construct($context);
    }

    public function displaySourceInventoryByCity($sku)
    {                
        $sourceItems = $this->getSourceItemsBySkuInterface->execute($sku);
        
        $sourceCodes = [];

        $sourceAndQty = [];

        foreach ($sourceItems as $sourceItem) {
            
            if(!in_array($sourceItem->getSourceCode(), $this->excludeSource)){

                $sourceCodes[] = $sourceItem->getSourceCode();
                $sourceAndQty[] = [
                    'source_code' => $sourceItem->getSourceCode(),
                    'qty' => $sourceItem->getQuantity() 
                ];
            }            
        }
        
        $list = $this->listStockByCities($sourceCodes, $sourceAndQty);        
        return $list;
    }

    public function displaySourceInventory($sku)
    {                
        $sourceItems = $this->getSourceItemsBySkuInterface->execute($sku);
        
        $sourceCodes = [];

        $sourceAndQty = [];

        foreach ($sourceItems as $sourceItem) {
            
            if(!in_array($sourceItem->getSourceCode(), $this->excludeSource)){

                $sourceCodes[] = $sourceItem->getSourceCode();
                $sourceAndQty[] = [
                    'source_code' => $sourceItem->getSourceCode(),
                    'qty' => $sourceItem->getQuantity() 
                ];
            }            
        }        
        $list = $this->listStockBySource($sourceCodes, $sourceAndQty);        
        return $list;
    }
    
    /**
     * @return string
     */
    public function listStockBySource($sourceCodes, $sourceAndQty)
    {        
        $sources = $this->sourceCollectionFactory->create()
        ->addFieldToSelect('*')
        ->addFieldToFilter('source_code', ['in' => $sourceCodes])->setOrder('city', 'ASC');        
        $sources = $sources->toArray();
        
        $city = [];        
        
        foreach ($sources['items'] as $source) {
            
            foreach ($sourceAndQty as $value) {
                
                if($value['source_code'] == $source['source_code']){
                    
                    $city[$source['city']][] = [

                        'source_code' => $source['source_code'],
                        'qty' => $value['qty'],
                        'name' => $source['name']

                    ];                    

                }                   

            }            
            
        }        

        $format = $this->formatStockBySource($city);

        return $format;
    }

    public function listStockByCities($sourceCodes, $sourceAndQty)
    {        
        $sources = $this->sourceCollectionFactory->create()
        ->addFieldToSelect(['source_code', 'city'])
        ->addFieldToFilter('source_code', ['in' => $sourceCodes])->setOrder('city', 'ASC');        
        $sources = $sources->toArray();
        
        $city = [];        
        
        foreach ($sources['items'] as $source) {
            
            $sum = 0;
            foreach ($sourceAndQty as $value) {
                
                if($value['source_code'] == $source['source_code']){
                                                          
                    $sum += $value['qty'];
                }                   

            }       
            
            $city[] = [
                'city' => $source['city'],
                'qty' => $sum
            ];
            
        }        
        
        $format = $this->formatStockByCity($city);

        return $format;
    }

    private function formatStockBySource($data){        
        
        $html = "<div class='content-inventory-cities-popup'>";
        $content = '';        
        $contentBody = '';                    
        foreach ($data as $key => $value) {                    
            $contentTotal = '<div class="col-cities-popup"><h4 class="col-title-cities-popup">%s</h4>%s %s</div>';   
            $sum = 0;
            $contentSumTotal = '<div class="sum-total-popup"><span class="label-sum-total">%s</span><span class="label-qty-total %s">%s</span></div>';            
            foreach ($value as $k => $v) {
                $tags = '<div class="row-per-office-popup"><span class="label-city-popup">%s</span><span class="label-qty %s">%s</span></div>';

                $qty = $v['qty'];
                $class = 'label-minor';
                $sum += $qty;
                if($qty > 20){
                    $qty = "+20";
                    $class = 'label-major';
                }                
                
                $content .= sprintf($tags, $v['name'], $class, $qty);   
            }   
            $classSum = 'label-minor'; 
            if($sum > 20){
                $sum = "+20";
                $classSum = 'label-major';
            }
            $contentSumTotal = sprintf($contentSumTotal, __("Total inventory"), $classSum, $sum);
            $contentBody .= sprintf($contentTotal, $key, $content, $contentSumTotal);                       
            $content = '';
        }
        
        $html .= $contentBody . '</div>';
        
        return $html;
    }

    private function formatStockByCity($data){

        $result = $this->processStockByCity($data);
        
        $html = "<div class='content-inventory-cities'>";
        $content = '';
        foreach ($result as $key => $value) {
            
            $tags = '<div class="col-per-city"><span class="label-city">%s</span><span class="label-qty %s">%s</span></div>';

            $qty = $value;
            $class = 'label-minor';
            if($value > 20){
                $qty = "+20";
                $class = 'label-major';
            }

            $content .= sprintf($tags, $key, $class, $qty);            
        }
        $html .= $content . '</div>';
        
        return $html;

    }

    private function processStockByCity($data){

        $return = array();
        $firstCity = '';
        $sum = 0;
        foreach($data as $val) {

            if($firstCity != $val['city']){
                $sum = 0;
                $sum += $val['qty'];

            }else{

                $sum += $val['qty'];
            }

            $return[$val['city']] = $sum;
            $firstCity = $val['city'];
        }

        return $return;

    }    

    public function displaySourceInventoryConfigurable($data){

        $sourceCodes = [];
    
        $sourceAndQty = [];

        foreach ($data as $value) {
            $sourceItems = $this->getSourceItemsBySkuInterface->execute($value['sku']);                    
    
            foreach ($sourceItems as $sourceItem) {
                
                if(!in_array($sourceItem->getSourceCode(), $this->excludeSource)){
    
                    $sourceCodes[] = $sourceItem->getSourceCode();
                    $sourceAndQty[] = [
                        'source_code' => $sourceItem->getSourceCode(),
                        'qty' => $sourceItem->getQuantity(),
                        'attributes' => $value['attributes'],
                        'name' =>$value['name'] 
                    ];
                }            
            }           
        }        
        $list = $this->listStockBySourceConfigurable($sourceCodes, $sourceAndQty);
        return $list;
    }

    public function listStockBySourceConfigurable($sourceCodes, $sourceAndQty)
    {        
        $sources = $this->sourceCollectionFactory->create()
        ->addFieldToSelect('*')
        ->addFieldToFilter('source_code', ['in' => $sourceCodes])->setOrder('city', 'ASC');        
        $sources = $sources->toArray();
        
        $city = [];        
        
        foreach ($sources['items'] as $source) {
            
            foreach ($sourceAndQty as $value) {
                
                if($value['source_code'] == $source['source_code']){
                    
                    $city[$source['city']][$source['name']][] = [

                        'name' => $value['name'],
                        'qty' => $value['qty'],
                        'attributes' => $value['attributes']

                    ];                    

                }                   

            }            
            
        }                
        $format = $this->formatStockBySourceConfigurable($city);

        return $format;
    }

    private function formatStockBySourceConfigurable($data){        
        
        $html = "<div class='content-inventory-cities-popup'>";
        $content = '';        
        $contentBody = '';                    
        foreach ($data as $key => $value) {                    
            $contentTotal = '<div class="col-cities-popup"><h4 class="col-title-cities-popup">%s</h4>%s %s</div>';   
            $sum = 0;
            $contentSumTotal = '<div class="sum-total-popup"><span class="label-sum-total">%s</span><span class="label-qty-total %s">%s</span></div>';     

            foreach ($value as $k => $v) {
                $contentproducts = '';           
                foreach ($v as $product) {
                    $tagProduct = '<div class="row-per-product-popup"><span class="label-product-popup">%s</span><span class="label-qty %s">%s</span></div>';
                    $qty = $product['qty'];
                    $class = 'label-minor';
                    $sum += $qty;
                    if($qty > 20){
                        $qty = "+20";
                        $class = 'label-major';
                    }  
                    $contentproducts .= sprintf($tagProduct, $product['attributes'], $class, $qty);   
                }
                $tags = '<div class="content-office"><div class="row-per-office-popup"><span class="label-city-popup">%s</span></div><div class="content-products">%s</div></div>';                               
                
                $content .= sprintf($tags, $k, $contentproducts);   
            }   
            $classSum = 'label-minor'; 
            if($sum > 20){
                $sum = "+20";
                $classSum = 'label-major';
            }
            $contentSumTotal = sprintf($contentSumTotal, __("Total inventory"), $classSum, $sum);
            $contentBody .= sprintf($contentTotal, $key, $content, $contentSumTotal);                       
            $content = '';
        }
        
        $html .= $contentBody . '</div>';
        
        return $html;
    }

    public function displaySourceInventoryListMaterial($sku){

        if($product = $this->productRepository->get($sku)){

            $list = '';

            if($product->getCustomAttribute('list_material')->getValue()){
                $sourceItems = $this->getSourceItemsBySkuInterface->execute($sku);
        
                $qty = 0;
        
                foreach ($sourceItems as $sourceItem) {
                    
                    if($sourceItem->getSourceCode() != 'default'){
                                
                        $qty += $sourceItem->getQuantity(); 
                    }            
                }
                
                $list = $this->formatQtyListMaterial($qty);        
            }            
            return $list;
        }        
    }

    public function formatQtyListMaterial($qty){
        $content = "<div class='content-inventory-cities'>";
        $tags = '<div class="col-per-lm"><span class="label-lm">%s</span><span class="label-qty">%s</span></div>';
        $tags = sprintf($tags, 'Unidades disponibles: ', $qty);
        $content .= $tags . '</div>';
        return $content;
    }

}

