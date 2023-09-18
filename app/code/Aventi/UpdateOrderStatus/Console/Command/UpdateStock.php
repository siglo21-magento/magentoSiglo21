<?php

namespace Aventi\UpdateOrderStatus\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateStock extends Command
{
    const NAME_OPTION = "UpdateStock";

    /**
     * @var \Aventi\UpdateOrderStatus\Model\UpdateStock
     */
    private $updateStock;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @param \Aventi\UpdateOrderStatus\Model\UpdateStock $updateStock
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Aventi\UpdateOrderStatus\Model\UpdateStock $updateStock,
        \Magento\Framework\App\State $state
    ) {
        parent::__construct(self::NAME_OPTION);
        $this->updateStock = $updateStock;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);
        $this->updateStock->setDefaultStock();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:updateStock");
        $this->setDescription("Update products in order stock");
        parent::configure();
    }
}
