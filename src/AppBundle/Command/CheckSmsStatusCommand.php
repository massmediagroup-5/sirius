<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckSmsStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sms:check')
            ->setDescription('Check sms statuses');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $this->getContainer()->get('distribution')->checkSmsStatus();

        $output->writeln($count . ' sms statuses was updated');
    }
}