<?php


namespace Aventi\SAP\Cron;

use Aventi\SAP\Model\Sync\Price;
use Bcn\Component\Json\Exception\ReadingError;
use Magento\Framework\Exception\FileSystemException;
use Psr\Log\LoggerInterface;

/**
 * Class PriceFaster
 *
 * @package Aventi\SAP\Cron
 */
class PriceFaster
{

    protected $logger;

    /**
     * @var \Aventi\SAP\Model\Sync\Price
     */
    private $price;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param Price $price
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Aventi\SAP\Model\Sync\Price $price
    ) {
        $this->logger = $logger;
        $this->price = $price;
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
        $this->logger->addInfo("Cronjob price faster is executed.");
        $this->price->updatePrice( true);
    }
}

