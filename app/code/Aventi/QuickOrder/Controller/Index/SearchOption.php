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

class SearchOption extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
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
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\Framework\Api\FilterBuilde
     */


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
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
        $this->request = $request;

        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->productRepository = $productRepository;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $items = [];
            $request = file_get_contents('php://input');
            if(!empty($request)) {

                $request = json_decode($request,true);
                $re = '/[^a-zA-Z0-9-_\. ]/m';
                $data =  $request['search'];
                $data = preg_replace($re, '', $data);

                if(!empty($data)){

                    $searchFields = ['sku', 'ref', 'name'];
                    $filterGroup = $this->filterGroupBuilder;
                    foreach ($searchFields as $field) {
                        /*if($field == 'name'){
                            $value = '%'.$data.'%';
                        }else{
                            $value = $data.'%';
                        }*/
                        $value = '%'.$data.'%';

                        $filterGroup->addFilter(
                            $this->filterBuilder
                                ->setField($field)
                                ->setConditionType('like')
                                ->setValue($value)
                                ->create()
                        );
                    }
                    $searchCriteria = $this->searchCriteriaBuilder
                        ->setFilterGroups([$filterGroup->create()])
                        ->setPageSize(30)
                        ->create();

                    $filter = $this->productRepository->getList($searchCriteria);
                    $products = $filter->getItems();

                    foreach ($products as $product){

                        if($product->isSalable()) {
                            $items[] = [
                                'd_search' => $data,
                                'name' => $product->getName(),
                                'sku' => $product->getSku(),
                                'ref' => $product->getData('ref')
                            ];
                        }
                    }

                }

            }
            return $this->jsonResponse($items);
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
