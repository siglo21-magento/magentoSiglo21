<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\CustomFilters\Observer\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Psr\Log\LoggerInterface;

class ProductSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private $_logger;
    /**
     * @var \Aventi\CustomFilters\Helper\Attribute
     */
    private $_helperAttribute;

    public function __construct(
        LoggerInterface $logger,
        \Aventi\CustomFilters\Helper\Attribute $helperAttribute
    ) {
        $this->_logger = $logger;
        $this->_helperAttribute = $helperAttribute;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $_product = $observer->getEvent()->getProduct();

        if ($_product) {
            //$this->_helperAttribute->updateProductStockStatus($_product);
        }
    }
}
