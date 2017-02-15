<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class: BindShareGroupsToSizesCommand
 *
 * @see ContainerAwareCommand
 */
class BindShareGroupsToSizesCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('shares:bind_groups_to_sizes')
            ->setDescription('Bind actual share groups to sizes');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('share')->bindActualShareGroupsToSizes();
    }
}
