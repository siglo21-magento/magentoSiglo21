<?php

namespace Aventi\SAP\Cron;

use Aventi\SAP\Model\Sync\Product;
use Psr\Log\LoggerInterface;

/**
 * Class ProductFaster
 *
 * @package Aventi\SAP\Cron
 */
class ProductFaster
{

    protected $logger;

    /**
     * @var \Aventi\SAP\Model\Sync\Product
     */
    private $product;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param Product $product
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Aventi\SAP\Model\Sync\Product $product
    ) {
        $this->logger = $logger;
        $this->product = $product;
    }

    /**
     * Execute the cron
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob Product Faster is executed.");
        $this->product->updateProduct(true);
    }
}
