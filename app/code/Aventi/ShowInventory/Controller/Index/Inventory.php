<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\ShowInventory\Controller\Index;

class Inventory extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    
    protected $jsonHelper;

    private $request;

    private $helper;

    private $productRepository; 

    private $productHelper;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Psr\Log\LoggerInterface $logger,
        \Aventi\ShowInventory\Helper\Data $helper,        
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Product $productHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        $this->request = $request;
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->productHelper = $productHelper;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $data = $this->request->getParam('sku');
            $result = '';
            $title = '';
            $img = '';
            if($product = $this->productRepository->get($data)){
                
                if($product->getTypeId() == 'simple'){
                    $result = $this->helper->displaySourceInventory($data);
                }                
                else if($product->getTypeId() == 'configurable'){
                    $childs = $product->getTypeInstance()->getUsedProducts($product);
                    $param = [];
                    $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
                    foreach ($childs as $child) {
                        
                        $attributeText = '';

                        foreach ($attributes as $attribute) {   
                            $child->load($child->getId());                         
                            $attr = $attribute->getProductAttribute()->getAttributeCode();
                            $attrText = $attribute->getProductAttribute()->getStoreLabel();
                            $val = $child->getResource()->getAttribute($attr)->getFrontend()->getValue($child);
                            $attributeText .= $attrText . ': ' . $val . ' ';    
                            $this->logger->error(json_encode($attribute->getProductAttribute()->getAttributeCode()));                        
                        }
                        $param[] = [
                            'sku' => $child->getSku(),
                            'id' => $child->getId(),
                            'name' => $child->getName(),
                            'attributes' => $attributeText
                        ];
                                                                        
                    }
                    $result = $this->helper->displaySourceInventoryConfigurable($param);
                }
                $title = $product->getName();
                $img = $this->productHelper->getThumbnailUrl($product);
            }
            $response = [
                'title' => $title,
                'img' => $img,
                'content' => $result
            ];
            return $this->jsonResponse($response);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
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

