<?php

namespace Aventi\SAP\Cron;

use Bcn\Component\Json\Exception\ReadingError;
use Magento\Framework\Exception\FileSystemException;
use Psr\Log\LoggerInterface;

/**
 * Class Price
 *
 * @package Aventi\SAP\Cron
 */
class Price
{

    protected $logger;

    /**
     * @var \Aventi\SAP\Model\Sync\Product
     */
    private $price;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param \Aventi\SAP\Model\Sync\Price $price
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
        $this->logger->addInfo("Cronjob price is executed.");
        $this->price->updatePrice(false);
    }
}
