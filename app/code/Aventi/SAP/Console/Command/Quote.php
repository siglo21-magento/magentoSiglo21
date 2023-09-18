<?php

namespace Aventi\SAP\Console\Command;

use Aventi\SAP\Model\Sync\SendToSAP;
use Magento\Framework\App\State;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Quote extends Command
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var SendToSAP
     */
    private $sendToSAP;

    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * @param State $state
     * @param SendToSAP $sendToSAP
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Aventi\SAP\Model\Sync\SendToSAP $sendToSAP,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct();
        $this->state = $state;
        $this->sendToSAP = $sendToSAP;
        $this->logger = $logger;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

        $this->sendToSAP->setOutput($output);

        try {
            $response = $this->sendToSAP->quoteToSAP();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $output->writeln("<error>{$e->getMessage()}(</error>");
        }
        if (is_array($response) && !empty($response)) {
            $table = new Table($output);
            $table
                ->setHeaders($response['title'])
                ->setRows([$response['body']]);
            $table->render();
        }
        $output->writeln("<info> finished :) number </info>");
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:sap:quote");
        $this->setDescription("Synchronize quote with SAP");
        parent::configure();
    }
}
