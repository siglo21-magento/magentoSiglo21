<?php

namespace Aventi\SAP\Cron;

use Aventi\SAP\Model\Sync\Stock as ManagerStock;
use Bcn\Component\Json\Exception\ReadingError;
use Magento\Framework\Exception\FileSystemException;

/**
 * Class StockListMaterial
 *
 * @package Aventi\SAP\Cron
 */
class StockListMaterial
{
    protected $logger;

    /**
     * @var ManagerStock;
     */
    private $stock;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param ManagerStock $stock
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        ManagerStock $stock
    ) {
        $this->logger = $logger;
        $this->stock = $stock;
    }

    /**
     * Execute the cron
     *
     * @return void
     * @throws ReadingError
     * @throws FileSystemException
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob StockListMaterial is executed.");
        $this->stock->updateStock('sales_stock', false, 'CDLM');
    }
}
