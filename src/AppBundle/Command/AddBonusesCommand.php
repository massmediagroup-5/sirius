<?php

namespace AppBundle\Command;

use AppBundle\Services\Order;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class: ClearCommand
 *
 * @see ContainerAwareCommand
 */
class AddBonusesCommand extends ContainerAwareCommand
{
    /**
     * @var Order
     */
    public $ordersService;
    
    /**
     * configure
     *
     */
    protected function configure()
    {
        $this
            ->setName('orders:add_bonuses')
            ->setDescription('Add bonuses to user');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * 
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ordersService = $this->getContainer()->get('order');

        $this->ordersService->appendBonusesToOrders();

        $output->writeln('Bonuses added!');
    }
}
