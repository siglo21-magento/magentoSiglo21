<?php
/**
 * Created by PhpStorm.
 * User: caguilar
 * Date: 14/08/18
 * Time: 14:46
 */

namespace Aventi\QuickOrder\Helper;


class Product extends \Magento\Framework\App\Helper\AbstractHelper
{    
    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    private $stockState;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,        
        \Magento\CatalogInventory\Api\StockStateInterface $stockState
    )
    {        
        parent::__construct($context);
        $this->stockState = $stockState;
    }   

    public function getStock($product){
        return  $this->stockState->getStockQty($product->getId());
    }


}