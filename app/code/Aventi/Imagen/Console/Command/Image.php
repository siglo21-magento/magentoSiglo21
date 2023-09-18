<?php


namespace Aventi\Imagen\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class Image extends Command
{


    /**
     * @var \Aventi\Imagen\Model\Process
     */
    private $process;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    public function __construct(
        \Aventi\Imagen\Model\Process $process,
        \Magento\Framework\App\State $state
    )
    {
        parent::__construct();
        $this->process = $process;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->state->setAreaCode( \Magento\Framework\App\Area::AREA_CRONTAB);
        $this->process->setOutput($output);
        $this->process->update();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:imagen:image");
        $this->setDescription("Update the  product images");

        parent::configure();
    }
}

