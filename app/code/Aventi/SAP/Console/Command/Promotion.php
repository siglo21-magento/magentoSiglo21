<?php

namespace Aventi\SAP\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Promotion
 *
 * @package Aventi\SAP\Console\Command
 */
class Promotion extends Command
{
    const PROCESS_SELECTED = 'Process';

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;
    private $promotionManager;

    public function __construct(
        \Aventi\SAP\Model\Sync\Promotion $promotionManager,
        \Magento\Framework\App\State $state
    ) {
        parent::__construct();
        $this->promotionManager = $promotionManager;
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
        $this->promotionManager->setOutput($output);
        $this->promotionManager->updatePromotion(false);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:sap:promotion");

        $this->setDescription("Sync promotion SAP");
        $this->setDefinition([
            new InputArgument(self::PROCESS_SELECTED, null, "Process"),
        ]);
        parent::configure();
    }
}
