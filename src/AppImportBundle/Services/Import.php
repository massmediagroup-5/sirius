<?php

namespace AppImportBundle\Services;

use Doctrine\ORM\EntityManager;
use Cocur\Slugify\Slugify;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class: Import
 *
 */
class Import
{

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * serviceContainer
     *
     * @var mixed
     */
    protected $serviceContainer = null;

    /**
     * vendor
     *
     * @var mixed
     */
    private $vendor;

    /**
     * vendorName
     *
     * @var mixed
     */
    private $vendorName;

    /**
     * worksheets
     *
     * @var mixed
     */
    private $worksheets;

    /**
     * title
     *
     * @var mixed
     */
    private $title;

    /**
     * titleLine
     *
     * @var mixed
     */
    private $titleLine;

    /**
     * titleColumn
     *
     * @var mixed
     */
    private $titleColumn;

    /**
     * column
     *
     * @var mixed
     */
    private $column;

    /**
     * methodData
     *
     * @var array
     */
    private $methodData = array();

    /**
     * importItemData
     *
     * @var array
     */
    private $importItemData = array();

    /**
     * output
     * ConsoleOutput.
     *
     * @var mixed
     */
    private $output;

    /**
     * __construct
     *
     * @param EntityManager $em
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(
        EntityManager $em,
        \Symfony\Component\DependencyInjection\Container $container
    )
    {
        set_time_limit(3600);
        $this->em = $em;
        $this->serviceContainer = $container;
        $this->output = new ConsoleOutput();
    }

    /**
     * __call
     *
     * @param mixed $name
     * @param mixed $value
     *
     * @return this
     */
    public function __call($name, $value)
    {
        $name = 'title' . substr($name, mb_strlen('setTitle'));
        if (isset($value[0])) {
            $this->methodData[$name] = $value[0];
        }
        return $this;
    }

    /**
     * clearEm
     *
     * Clear Em and reinit some Entities
     *
     * @return this
     */
    public function clearEm()
    {
        $this->em->clear();
        $this->setVendor($this->vendor);
        return $this;
    }

    /**
     * setVendor
     *
     * We must set the specific Vendor before start save the data.
     *
     * @param mixed $vendorName
     *
     * @return this
     */
    public function setVendor($vendorName)
    {
        $this->vendor = $vendorName;
        $this->vendorObj = $this->em->getRepository('AppBundle:Vendors')
            ->findOneByName($vendorName);
        return $this;
    }

    /**
     * setWorksheets
     *
     * We must define Worksheets before starting import.
     *
     * @param array $worksheets
     *
     * @return this
     */
    public function setWorksheets(array $worksheets = array())
    {
        $this->worksheets = $worksheets;
        return $this;
    }

    /**
     * setTitleLine
     *
     * We must define TitleLine before starting import.
     *
     * @param int $line
     *
     * @return this
     */
    public function setTitleLine($line = NULL)
    {
        $this->titleLine = $line;
        return $this;
    }

    /**
     * import
     *
     * @param mixed $file
     *
     * @return null
     */
    public function import($file)
    {
        $productModels = array();
        $this->output->writeln('<info>Start importing file '. $file .'</info>');
        $phpExcelObject = $this->serviceContainer->get('phpexcel')
            ->createPHPExcelObject($file);
        foreach ($phpExcelObject->getWorksheetIterator() as $worksheet) {
            // use only the set worksheets
            if (in_array($worksheet->getTitle(), $this->worksheets, TRUE)) {
                // set columns for active worksheet.
                $this->initWorksheetTile($worksheet);
                // looping for all rows
                $highestRow = $worksheet->getHighestRow();
                // Split all ProductModels for Products
                $progress = new ProgressBar($this->output, $highestRow - $this->titleLine);
                $progress->setFormat('debug');
                $progress->start();
                for ($row = $this->titleLine + 1; $row <= $highestRow; $row++) {
                    $rowValue = $this->getLine($worksheet, $row);
                    $skuProduct = $this->updateOrInsertProduct($rowValue);
                    if ($skuProduct !== null) {
                        $productModels[] = $skuProduct->getProductModels()->getId();
                    }
                    $progress->advance();
                }
                $progress->finish();
                $this->output->writeln("\n");
                $this->em->flush();
                $this->clearEm();
            }
        }
        return $productModels;
    }

    /**
     * updateOrInsertProduct
     *
     * @param mixed $rowValue
     */
    private function updateOrInsertProduct($rowValue)
    {
        // set Item data for current item row
        $this->setImportItemData($rowValue);
        $rowTitleSKU = $this->importItemData['titleSKU'];
        $skuProduct = null;
        if (!empty($rowTitleSKU)
            && !empty($this->importItemData['titleName'])
        ) {
            $skuProduct = $this->em
                ->getRepository('AppBundle:SkuProducts')
                ->findOneSkuForVendor($rowTitleSKU, $this->vendor);
            if (empty($skuProduct)) {
                $skuProduct = $this->initTheSequenceOfProduct($rowTitleSKU);
            }
            $skuProduct
                ->setName($this->importItemData['titleName'])
                ->setStatus(0)
                ->setActive(0)
                ->setQuantity(1)
                ;
            // Check if Price column exists.
            if ($this->titleColumn['titlePrice'] && !empty($this->titleColumn['titlePrice'])) {
                $price = $this->importItemData['titlePrice'];
                $skuProduct
                    ->setPrice($price)
                    ;
            }
            $skuProduct->getProductModels()->setInStock(1);
            $this->em->persist($skuProduct);
        }
        return $skuProduct;
    }

    /**
     * setTitle
     *
     * @param mixed $worksheet
     */
    private function setTitle($worksheet)
    {
        $this->title = $this->getLine($worksheet, $this->titleLine);
    }

    /**
     * getLine
     *
     * @param mixed $worksheet
     * @param mixed $lineNumber
     *
     * @return array
     */
    private function getLine($worksheet, $lineNumber)
    {
        $column = $this->column;
        $line = array();
        for ($i = 0; $i < count($this->column); $i++) {
            $line[$column[$i]] = $worksheet->getCell($column[$i] . $lineNumber)
                ->getCalculatedValue();
        }
        return $line;
    }

    /**
     * initWorksheetTile
     *
     * @param mixed $worksheet
     */
    private function initWorksheetTile($worksheet)
    {
        $this->column = array_keys($worksheet->getColumnDimensions());
        // set Title
        $this->setTitle($worksheet);
        foreach ($this->methodData as $columnKey => $columnValue) {
            $item = array_search($this->methodData[$columnKey], $this->title);
            $this->titleColumn[$columnKey] = $item;
        }
        //dump($this->titleColumn);
    }

    /**
     * setImportItemData
     *
     * @param mixed $row
     */
    private function setImportItemData($row)
    {
        foreach ($this->titleColumn as $titleName => $titleColumn) {
            if ($titleColumn) {
                $this->importItemData[$titleName] = $row[$titleColumn];
            }
        }
    }

    /**
     * initTheSequenceOfProduct
     *
     * @param mixed $rowTitleSKU
     *
     * @return \AppBundle\Entity\SkuProducts $skuProduct
     */
    private function initTheSequenceOfProduct($rowTitleSKU)
    {
        $product = $this->findProduct($rowTitleSKU);
        // init new ProductModel
        $alias = $this->uniqAlias();
        $productModel = new \AppBundle\Entity\ProductModels;
        $productModel
            ->setProducts($product)
            ->setProductColors(
                $this->em->getRepository('AppBundle:ProductColors')
                    ->findOneByName('none')
            )
            ->setName($this->importItemData['titleName'])
            ->setAlias($alias)
            ->setStatus(0)
            ->setActive(0)
            ;
        // Check if Price column exists.
        if ($this->titleColumn['titlePrice']) {
            $price = $this->importItemData['titlePrice'];
            $oldPrice = $price * 1.1;
            $productModel
                ->setPrice($price)
                ->setOldprice($oldPrice)
                ;
        }
        $this->em->persist($productModel);
        $this->em->flush();
        // init new SkuProduct
        $skuProduct = new \AppBundle\Entity\SkuProducts;
        $skuProduct
            ->setSku($rowTitleSKU)
            ->setVendors($this->vendorObj)
            ->setProductModels($productModel)
            ;
        return $skuProduct;
    }

    /**
     * findProduct
     *
     * @param mixed $rowTitleSKU
     *
     * @return \AppBundle\Entity\Products
     */
    private function findProduct($rowTitleSKU)
    {
        // Cut the Product Name for last digit.
        preg_match('/^(.*)\d+/', $this->importItemData['titleProductName'], $skuName);
        $skuName = (isset($skuName[0]))
            ? $skuName[0]
            : $this->importItemData['titleProductName'];
        //dump($this->importItemData['titleName'], $skuName);
        $product = $this->em->getRepository('AppBundle:Products')
            ->findOneByImportName($skuName);
        //dump($product);
        if (!$product) {
            // init new Product
            $product = new \AppBundle\Entity\Products;
            $product
                ->setImportName($skuName)
                ->setActionLabels(
                    $this->em->getRepository('AppBundle:ActionLabels')
                        ->findOneByName('none')
                )
                ->setStatus(0)
                ->setActive(0)
                ;
            $this->em->persist($product);
            $this->em->flush();
        }
        return $product;
    }

    /**
     * uniqAlias
     *
     * @return string
     */
    private function uniqAlias()
    {
        $slugify = new Slugify();
        $firstAlias = $slugify->slugify($this->importItemData['titleName']);
        $alias = $firstAlias;
        $counter = 1;
        while (!empty($this->em->getRepository('AppBundle:ProductModels')
            ->findOneByAlias($alias))
        ) {
            $alias = $firstAlias . $counter;
            $counter++;
        }
        //dump($alias);
        return $alias;
    }

}
