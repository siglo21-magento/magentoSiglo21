<?php

namespace Aventi\SAP\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Price
 *
 * @package Aventi\SAP\Console\Command
 */
class Price extends Command
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
        $this->_priceManager->updatePrice(0);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:sap:price");
        $this->setDescription("Sync price SAP to Magento");
        parent::configure();
    }
}
