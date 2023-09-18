<?php

namespace Aventi\SAP\Cron;

use Bcn\Component\Json\Exception\ReadingError;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;
use Psr\Log\LoggerInterface;

/**
 * Class CustomerFast
 *
 * @package Aventi\SAP\Cron
 */
class Customer
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
     * @param \Aventi\SAP\Model\Sync\Customer $customer
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Aventi\SAP\Model\Sync\Customer $customer
    ) {
        $this->logger = $logger;
        $this->customer = $customer;
    }

    /**
     * Execute the cron
     *
     * @return void
     * @throws ReadingError
     * @throws FileSystemException
     * @throws InputException
     * @throws LocalizedException
     * @throws InputMismatchException
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob Customer is executed.");
        $this->customer->company(false);
    }
}

