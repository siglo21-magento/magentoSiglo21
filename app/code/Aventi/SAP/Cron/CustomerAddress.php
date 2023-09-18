<?php

namespace Aventi\SAP\Cron;

use Aventi\SAP\Model\Sync\Customer;
use Bcn\Component\Json\Exception\ReadingError;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class CustomerAddress
 *
 * @package Aventi\SAP\Cron
 */
class CustomerAddress
{

    protected $logger;

    /**
     * @var \Aventi\SAP\Model\Sync\Customer
     */
    private $customer;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param Customer $customer
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Aventi\SAP\Model\Sync\Customer $customer
    )
    {
        $this->logger = $logger;
        $this->customer = $customer;
    }

    /**
     * Execute the cron
     *
     * @return void
     * @throws ReadingError
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob Customer Address is executed.");
        $this->customer->managerCustomerAddress(false);
    }
}

