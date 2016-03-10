<?php

namespace AppBundle\Command;

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
class ClearCommand extends ContainerAwareCommand
{

    /**
     * logger
     *
     * @var mixed
     */
    private $logger;

    /**
     * configure
     *
     */
    protected function configure()
    {
        $this
            ->setName('clear:db')
            ->setDescription('Clear ALL Database!!!')
        ;
    }

    /**
     * execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger = $this->getContainer()->get('logger');
        $this->logger->info('Start to Clear database.');

        $entities = [
            'Cart',
            'Orders',
            'CallBack',
            'Wishlist',
            'SkuProducts',
            'ProductModelImages',
            'ProductModels',
            'ProductColors',
            'ProductModelSizes',
            'Products',
            'Categories',
            'Filters',
            'Characteristics',
            'CharacteristicValues',
            'CharacteristicGroups',
        ];
        $tables = [
            'products_has_characteristic_values',
            'products_has_categories',
            'categories_has_characteristics',
            'categories_has_characteristic_values',
            'filters_has_characteristics',
//            'sessions',
        ];
        $fields = [
            'ProductColors'  => [
                'none',
            ],
            'Filters' => [
                'all',
            ],
            'Categories' => [
                'Базовая',
            ],
        ];

        $this->clearDb($entities, $tables, $fields);

        $this->logger->info('Clearing database was finished!');
        $output->writeln('Clearing database was finished!');
    }

    /**
     * clearDb
     *
     * @param array $entities
     * @param array $tables
     * @param array $fields
     *
     * @return boolean
     */
    private function clearDb(array $entities, array $tables = array(), array $fields = array())
    {
        return $this->getContainer()->get('clearDb')
            ->setEntities($entities)
            ->setTables($tables)
            ->setNotDeletedFields($fields)
            ->clear();
    }

}
