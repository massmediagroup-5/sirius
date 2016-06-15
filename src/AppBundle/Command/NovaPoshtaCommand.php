<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class: NovaPoshtaCommand
 *
 * @see ContainerAwareCommand
 */
class NovaPoshtaCommand extends ContainerAwareCommand
{

	/**
	 * logger
	 *
	 * @var mixed
	 */
	private $logger;

	/**
	 * configure
	 */
	protected function configure()
	{
		$this
			->setName('np:update')
			->setDescription('Update cities and warehouses (Novaposhta API)')
		;
	}

	/**
	 * execute this command
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return null
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->logger = $this->getContainer()->get('logger');
		$this->logger->info('Start updating database.');
		$output->writeln('Start updating database.');

//		$this->getContainer()->get('NovaPoshta')->insertDatabase();
		$this->getContainer()->get('NovaPoshta')->updateDatabase();

		$this->logger->info('Updating database was finished!');
		$output->writeln('');
		$output->writeln('Updating database was finished!');
	}

}
