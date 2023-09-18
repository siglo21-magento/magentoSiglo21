<?php

namespace Aventi\SAP\Cron;

use Aventi\SAP\Model\Sync\Stock as ManagerStock;
use Bcn\Component\Json\Exception\ReadingError;
use Magento\Framework\Exception\FileSystemException;

/**
 * Class Stock
 *
 * @package Aventi\SAP\Cron
 */
class Stock
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
    )
    {
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
        $this->logger->addInfo("Cronjob Stock is executed.");
        $this->stock->updateStock('stock', false, null);
    }
}

