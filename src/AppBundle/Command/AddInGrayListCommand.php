<?php

namespace AppBundle\Command;

use AppBundle\Services\Order;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class: AddInGrayListCommand
 *
 * @see ContainerAwareCommand
 */
class AddInGrayListCommand extends ContainerAwareCommand
{
    /**
     * configure
     *
     */
    protected function configure()
    {
        $this
            ->setName('users:add_in_gray_list')
            ->setDescription('Add users to gray list');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * 
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userService = $this->getContainer()->get('users');

        $users = $this->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Users')
            ->findAllWithLastOrderOlderThan(90);

        foreach ($users as $user) {
            $userService->toGrayList($user);
        }
    }
}
