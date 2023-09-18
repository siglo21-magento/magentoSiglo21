<?php
/**
 * Copyright © Aventi SAS All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aventi\SAP\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Aventi\SAP\Model\Sync\Stock as ManagerStock;
/**
 * Class Stock
 *
 * @package Aventi\SAP\Console\Command
 */
class Stock extends Command
{

    const PROCESS_SELECTED = 'Process';

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @var ManagerStock;
     */
    private $_stockManager;


    public function __construct
    (
        \Magento\Framework\App\State $state,
        ManagerStock $stockManager
    ) {
        parent::__construct();
        $this->state = $state;
        $this->_stockManager = $stockManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $process = (int) $input->getArgument(self::PROCESS_SELECTED);
        if( $process < 0 || $process > 2){
            $output->writeln("<error>Process no found :(</error>");
            exit;
        }
        switch ($process) {
            case 1:
                $params = ['type' => 'stock', 'fast' => true, 'source' => null];
                break;
            case 2:
                $params = ['type' => 'sales_stock', 'fast' => false, 'source' => 'CDLM'];
                break;
            default:
                $params = ['type' => 'stock', 'fast' => false, 'source' => null];
                break;
        }
        $this->state->setAreaCode( \Magento\Framework\App\Area::AREA_CRONTAB);
        $this->_stockManager->setOutput($output);
        $this->_stockManager->updateStock($params['type'], $params['fast'], $params['source']);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:sap:stock");
        $this->setDescription("Sync stock SAP to Magento 0 => Stock, 1 => Fast Stock, 2 => List Materials");
        $this->setDefinition([
            new InputArgument(self::PROCESS_SELECTED, null, "Process"),
        ]);
        parent::configure();
    }
}

