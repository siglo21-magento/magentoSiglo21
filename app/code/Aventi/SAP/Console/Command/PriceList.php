<?php

namespace Aventi\SAP\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PriceList
 * @package Aventi\SAP\Console\Command
 */
class PriceList extends Command
{

    /**
     * @var \Aventi\SAP\Model\Sync\Product
     */
    private $_priceManager;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    public function __construct(
        \Aventi\SAP\Model\Sync\Price $price,
        \Magento\Framework\App\State $state
    ) {
        parent::__construct();
        $this->_priceManager = $price;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);
        $this->_priceManager->setOutput($output);
        $this->_priceManager->updatePriceList();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:sap:pricelist");
        $this->setDescription("Sync price list from SAP");
        parent::configure();
    }
}
