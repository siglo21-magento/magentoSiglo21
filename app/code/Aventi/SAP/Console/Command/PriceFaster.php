<?php

namespace Aventi\SAP\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PriceFaster
 *
 * @package Aventi\SAP\Console\Command
 */
class PriceFaster extends Command
{

    /**
     * @var \Aventi\SAP\Model\Sync\Price
     */
    private $priceManager;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    public function __construct(
        \Aventi\SAP\Model\Sync\Price $price,
        \Magento\Framework\App\State $state
    ) {
        parent::__construct();
        $this->priceManager = $price;
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
        $this->priceManager->setOutput($output);
        $this->priceManager->updatePrice(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:sap:pricefaster");
        $this->setDescription("Sync price SAP to Magento");
        parent::configure();
    }
}
