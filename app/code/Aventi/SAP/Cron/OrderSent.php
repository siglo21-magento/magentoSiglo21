<?php

namespace Aventi\SAP\Cron;

use Aventi\SAP\Model\Sync\SendToSAP;
use Magento\Framework\Exception\MailException;
use Psr\Log\LoggerInterface;

/**
 * Class OrderSent
 *
 * @package Aventi\SAP\Cron
 */
class OrderSent
{

    protected $logger;

    /**
     * @var \Aventi\SAP\Model\Sync\SendToSAP
     */
    private $sendToSAP;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param SendToSAP $sendToSAP
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Aventi\SAP\Model\Sync\SendToSAP $sendToSAP
    ) {
        $this->logger = $logger;

        $this->sendToSAP = $sendToSAP;
    }

    /**
     * Execute the cron
     *
     * @return void
     * @throws MailException
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob OrderSent is executed.");
        $this->sendToSAP->orderSent();
    }
}
