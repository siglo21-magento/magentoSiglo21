<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\CustomFilters\Plugin\Magento\InventoryApi\Api;

use Aventi\CustomFilters\Helper\Attribute;
use Aventi\CustomFilters\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class SourceItemsSaveInterface
{

    /**
     * @var \Aventi\CustomFilters\Helper\Attribute
     */
    private $_helperAttribute;
    /**
     * @var LoggerInterface
     */
    private $_logger;
    /**
     * @var Data
     */
    private $_helper;
    /**
     * @var ProductRepositoryInterface
     */
    private $_productRepository;

    public function __construct(
        LoggerInterface $logger,
        Attribute $helperAttribute,
        ProductRepositoryInterface $productRepository,
        Data $helper
    ) {
        $this->_logger = $logger;
        $this->_helperAttribute = $helperAttribute;
        $this->_helper = $helper;
        $this->_productRepository = $productRepository;
    }

    /**
     * @param \Magento\InventoryApi\Api\SourceItemsSaveInterface $subject
     * @param $result
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterface[] $sourceItems
     * @return mixed
     */
    public function afterExecute(
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $subject,
        $result,
        array $sourceItems
    ) {
        $cont = 0;
        $sku = '';
        foreach ($sourceItems as $item) {
            $sku = $item->getSku();
            if ($item->getStatus() == 1) {
                $cont += $item->getQuantity();
            }
        }
        try {
            $product = $this->_productRepository->get($sku);
            $this->_helperAttribute->updateProductStockStatus($product, $cont);
        } catch (NoSuchEntityException $e) {
            $this->_logger->error("ERROR STOCK ITEM SAVE INTERFACE: " . $e->getMessage());
        }
        return $result;
    }
}
