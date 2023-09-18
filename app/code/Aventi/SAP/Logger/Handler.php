<?php

namespace Aventi\SAP\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level.
     * @var int
     */
    protected $loggerType = \Monolog\Logger::DEBUG;

    /**
     * File name.
     * @var string
     */
    protected $fileName = "/var/log/aventi_sap_info.log";
}
