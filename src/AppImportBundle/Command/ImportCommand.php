<?php

namespace AppImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class: ImportCommand
 *
 * @see ContainerAwareCommand
 */
class ImportCommand extends ContainerAwareCommand
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
            ->setName('import:file')
            ->setDescription('Start import')
            ->addArgument(
                'filename',
                InputArgument::OPTIONAL,
                'Whick file to import?'
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
        $filename = $input->getArgument('filename');
        $filename = empty($filename) ? "import.xls" : $filename;

        $dir = $this->getContainer()->get('kernel')->getRootDir();
        $dir = realpath($dir . '/../web/import/') . '/';
        $filepath = $dir . $filename;
        // Check if file exist
        if (!file_exists($filepath))
          die("Import already runninng\n");
        // ---
        // Check import
        if(!flock(fopen($dir . ".import.lock", 'w'), LOCK_EX | LOCK_NB))
          die("Import already runninng\n");
        // ---
        // Start importing
        $this->logger->info('Start Importing file ' . $filepath);

        $this->ImportingAction($filepath);

        $this->logger->info('Was finished Importing file ' . $filepath);
        $output->writeln('Import ' . $filepath . ' was finished!');
    }

    /**
     * ImportingAction
     *
     * @param mixed $importFileName
     * @return null
     */
    private function ImportingAction($importFileName)
    {
        $productModels = $this->getContainer()->get('import')
            ->setVendor('yugcontract')
            ->setWorksheets(array(
                //'КОМПЬЮТЕРНАЯ ТЕХНИКА',
                'БЫТОВАЯ ТЕХНИКА'
            ))
            ->setTitleLine(10)
            ->setTitleSKU('Артикул')
            ->setTitleName('Наименование рус.')
            ->setTitleProductName('Наименование англ.')
            ->setTitlePrice('Цена на сайт')
            ->import($importFileName)
            ;
        $this->getContainer()->get('update_all')
            ->unpublishedProductModelsExcept($productModels);
        $this->getContainer()->get('import')
            ->setVendor('yugcontract')
            ->putNotParsed($productModels);
        return null;
    }

}
