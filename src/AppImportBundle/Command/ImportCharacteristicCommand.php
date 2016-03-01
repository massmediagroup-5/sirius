<?php

namespace AppImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class: ImportCharacteristicCommand
 *
 * @see ContainerAwareCommand
 */
class ImportCharacteristicCommand extends ContainerAwareCommand
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
            ->setName('import:characteristic')
            ->setDescription('Start import characteristics')
            ->addOption(
                'persist-categories',
                null,
                InputOption::VALUE_NONE,
                'Update or insert new Category from importing data.'
            )
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

        $dir = $this->getContainer()->get('kernel')->getRootDir();
        $dir = realpath($dir . '/../web/import/') . '/';
        // Check import
        if(!flock(fopen($dir . ".import.lock", 'w'), LOCK_EX | LOCK_NB))
          die("Import already runninng\n");
        // ---
        // Start importing
        $this->logger->info('Start Importing of characteristics from DB');

        $categoryPersist = $input->getOption('persist-categories') ? true : false;
        $countRow = $this->CharacteristicsImportAction($categoryPersist);

        $this->logger->info('End Importing ' . $countRow . ' rows of characteristics from DB');
        $output->writeln('<info>Import characteristics was finished!</info>');
    }

    /**
     * CharacteristicsImportAction
     *
     * @param boolean $categoryPersist
     *
     * @return null
     */
    private function CharacteristicsImportAction($categoryPersist)
    {
        $data = $this->getContainer()->get('importCharacteristics')
            ->setTabelCharacteristics('specnames')
            ->setTabelCharacteristicsGroups('specgroup')
            ->setTabelCharacteristicValues('mytex.test')
            ->setTabelModels('models')
            ->setTabelNotParsed('not_parsed')
            ->setCharacteristicValuesName('naimenovanie')
            ->setImgUrl('foto')
            ->setCategory('category')
            ->setModelNameInChV('URL')
            ->setModelNameInModel('model')
            ->putBrandNameInChV('Proizvoditel')
            ->setModelArticulInModel('artikyl')
            ->setNotCharacteristics('speclist')
            ->setNumerator('id')
            ->setColumnColor('TSvet')
            ->setPersistCategories($categoryPersist)
            ->import()
            ;
        $this->getContainer()->get('checkRelationship')
            ->checkVsProducts();
        return null;
    }

}
