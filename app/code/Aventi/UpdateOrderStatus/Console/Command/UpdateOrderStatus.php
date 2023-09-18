<?php
/**
 * Copyright © Aventi All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\UpdateOrderStatus\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateOrderStatus extends Command
{
    const NAME_OPTION = "UpdateOrderStatus";

    /**
     * @var \Aventi\UpdateOrderStatus\Model\OrderStatus
     */
    private $updateOrderStatus;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @param \Aventi\UpdateOrderStatus\Model\OrderStatus $orderStatus
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Aventi\UpdateOrderStatus\Model\OrderStatus $orderStatus,
        \Magento\Framework\App\State $state
    ) {
        parent::__construct(self::NAME_OPTION);
        $this->updateOrderStatus = $orderStatus;
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
        $this->updateOrderStatus->changeStatus();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:UpdateOrderStatus");
        $this->setDescription("Update orders status");
        parent::configure();
    }
}

