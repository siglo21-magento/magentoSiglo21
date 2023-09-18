<?php
namespace Aventi\CustomFilters\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Psr\Log\LoggerInterface;

/**
 * Class Data
 * @package Aventi\CustomFilters\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;
    /**
     * @var GetSourceItemsBySkuInterface
     */
    private $_getSourceItemsBySku;
    /**
     * @var GetSalableQuantityDataBySku
     */
    private $_getSalableQuantityDataBySku;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param GetSourceItemsBySkuInterface $getSourceItemsBySku
     * @param LoggerInterface $logger
     * @param GetSalableQuantityDataBySku $getSalableQuantityDataBySku
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        GetSourceItemsBySkuInterface $getSourceItemsBySku,
        LoggerInterface $logger,
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku
    ) {
        $this->_getSourceItemsBySku = $getSourceItemsBySku;
        $this->_logger = $logger;
        $this->_getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
        parent::__construct($context);
    }

    /**
     * @param $sku
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface[]|null
     */
    public function getStockItems($sku)
    {
        try {
            return $this->_getSourceItemsBySku->execute($sku);
        } catch (NoSuchEntityException $e) {
            $this->_logger->error("Stock by product id don't found: " . $e->getMessage());
            return null;
        }
    }

    public function getSalableQuantity($sku)
    {
        $salableQty = $this->_getSalableQuantityDataBySku->execute($sku);
        $qty = 0;
        if (is_array($salableQty)) {
            foreach ($salableQty as $salable) {
                $qty += $salable['qty'];
            }
        }
        return $qty;
    }
}
