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
 * Class CustomerAddress
 *
 * @package Aventi\SAP\Console\Command
 */
class CustomerAddress extends Command
{

    const PROCESS_SELECTED = 'Process';

    const PROCESS_LIST = [
        0 => 'Customer Address Fast',
        1 => 'Customer Address'
    ];
    /**
     * @var \Aventi\SAP\Model\Sync\Customer
     */
    private $customerManager;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    public function __construct(\Aventi\SAP\Model\Sync\Customer $customer,
                                \Magento\Framework\App\State $state)
    {
        parent::__construct();
        $this->customerManager = $customer;
        $this->state = $state;
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $process = (int)$input->getArgument(self::PROCESS_SELECTED);
        if( $process < 0 || $process > 1){
            $output->writeln("<error>Process no found :(</error>");
            exit;
        }
        $this->state->setAreaCode( \Magento\Framework\App\Area::AREA_CRONTAB);
        $this->customerManager->setOutput($output);
        $this->customerManager->managerCustomerAddress($process);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:sap:customer_address");
        $this->setDescription("Sync customer address SAP to Magento [
            0 => Customer Address,
            1 => Customer Address Fast
        ]");
        $this->setDefinition([
            new InputArgument(self::PROCESS_SELECTED, null, "Process"),
        ]);
        parent::configure();
    }
}

