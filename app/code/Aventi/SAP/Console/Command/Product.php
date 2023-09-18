<?php


namespace Aventi\SAP\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;

/**
 * Class Product
 *
 * @package Aventi\SAP\Console\Command
 */
class Product extends Command
{
    const PROCESS_SELECTED = 'Process';

    /**
     * @var \Aventi\SAP\Model\Sync\Product
     */
    private $productManager;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    public function __construct(
        \Aventi\SAP\Model\Sync\Product $product,
        \Magento\Framework\App\State $state
    ) {
        parent::__construct();
        $this->productManager = $product;
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
        $process = (int)$input->getArgument(self::PROCESS_SELECTED);
        if ( $process < 0 || $process > 1) {
            $output->writeln("<error>Process no found :(</error>");
            exit;
        }
        $this->productManager->setOutput($output);
        $this->productManager->updateProduct($process);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:sap:product");
        $this->setDescription("Sync product SAP to Magento. 0 => Product, 1 => Fast Product");
        $this->setDefinition([
            new InputArgument(self::PROCESS_SELECTED, null, "Process"),
        ]);
        parent::configure();
    }
}

