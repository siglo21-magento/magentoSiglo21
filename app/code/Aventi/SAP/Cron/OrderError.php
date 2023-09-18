<?php

namespace Aventi\SAP\Cron;

use Aventi\SAP\Model\Sync\SendToSAP;
use Psr\Log\LoggerInterface;

/**
 * Class Price
 *
 * @package Aventi\SAP\Cron
 */
class OrderError
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
     */
    public function execute()
    {
        $this->sendToSAP->errorOrderToSAP();
    }
}
