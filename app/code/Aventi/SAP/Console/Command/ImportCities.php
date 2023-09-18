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
class ImportCities extends Command
{


    /**
     * @var \Aventi\SAP\Model\Sync\Customer
     */
    private $customerManager;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var \Aventi\SAP\Helper\Config
     */
    private $config;

    public function __construct(
        \Aventi\SAP\Model\Sync\Customer $customer,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Aventi\SAP\Helper\Config $config
    ) {
        parent::__construct();
        $this->customerManager = $customer;
        $this->state = $state;
        $this->resourceConnection = $resourceConnection;
        $this->config = $config;
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {

        $this->state->setAreaCode( \Magento\Framework\App\Area::AREA_CRONTAB);

        $path = $this->config->getPathCsvCities();

        $row = 1;
        $setup = $this->resourceConnection;
        if (($handle = fopen($path, "r")) !== FALSE) {

            while (($data = fgetcsv($handle, 1000, ",", '"')) !== FALSE) {

                $table_postal_code='aventi_citydropdown_city';
                $newStr = $this->config->stripAccents($data[1]);
                $bind = ['name' => strtoupper($newStr), 'postalCode' => $data[0], 'region_id' => (int) $data[2]];
                        $setup->getConnection()->insert($table_postal_code, $bind);
            }
            fclose($handle);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:sap:cities");
        $this->setDescription("Sync cities SAP to Magento");
        parent::configure();
    }
}

