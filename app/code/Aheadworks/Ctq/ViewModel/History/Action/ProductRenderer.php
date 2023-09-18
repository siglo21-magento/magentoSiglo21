<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\History\Action;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class ProductRenderer
 * @package Aheadworks\Ctq\ViewModel\History\Action
 */
class ProductRenderer implements ArgumentInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Retrieve product name by id
     *
     * @param int $productId
     * @return string
     */
    public function getProductName($productId)
    {
        try {
            $product = $this->productRepository->getById($productId);
            $productName = $product->getName();
        } catch (NoSuchEntityException $e) {
            $productName = 'Undefined';
        }
        return $productName;
    }
}
