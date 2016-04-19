<?php

namespace AppAdminBundle\Admin;

use AppBundle\Entity\Categories;
use AppBundle\Entity\CharacteristicableInterface;
use AppBundle\Entity\ProductModels;
use AppBundle\Entity\Products;
use AppBundle\Entity\Vendors;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Str;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImportAdmin
{
    /**
     * Number of article row
     */
    const ARTICLE_ROW = 3;

    /**
     * Map column letter to column meaning
     *
     * @var array
     */
    private $listColumnsMap = [
        'A' => 'sku',
        'B' => 'count',
        'C' => 'sizes',
        'D' => 'wholesale_price',
        'E' => 'price',
        'F' => 'color',
        'G' => 'decoration_color',
    ];

    /**
     * Map row number to row meaning
     *
     * @var array
     */
    private $infoRowsMap = [
        2 => 'model',
        4 => 'type',
        5 => 'category',
        6 => 'material',
        7 => 'composition',
        8 => 'description',
        9 => 'features',
        10 => 'parameters',
        11 => 'action',
        12 => 'discount',
        13 => 'sale',
        14 => 'season',
        15 => 'wholesale_min',
        16 => 'packaging_contain',
    ];

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * @var Vendors
     */
    private $vendor;

    /**
     * @var \PHPExcel $phpExcelObj
     */
    private $phpExcelObj;

    /**
     * @var \PHPExcel_Worksheet $infoSheet
     */
    private $infoSheet;

    /**
     * @var \PHPExcel_Worksheet $listSheet
     */
    private $listSheet;

    /**
     * @var array $currentRowData
     */
    private $currentRowData;

    /**
     * @var RecursiveValidator
     */
    protected $validator;

    /**
     * @var Collection
     */
    protected $validationRules;

    /**
     * @var \AppBundle\Entity\Categories
     */
    protected $baseCategory;

    /**
     * @var \AppBundle\Entity\Filters
     */
    protected $allFilter;

    /**
     * Publish all imported data
     *
     * @var bool
     */
    protected $publishFlag = true;

    /**
     * @param EntityManager $em
     * @param ContainerInterface $container
     * @param RecursiveValidator $validator
     */
    public function __construct(EntityManager $em, ContainerInterface $container, RecursiveValidator $validator)
    {
        $this->em = $em;
        $this->container = $container;
        $this->validator = $validator;
        $this->slugify = new Slugify();
    }

    /**
     * Currently only one vendor.
     *
     * @param Vendors $vendor
     * @return $this
     */
    public function setVendor(Vendors $vendor)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Import
     *
     * @param $file
     */
    public function import($file)
    {
        $this->phpExcelObj = $this->container->get('phpexcel')
            ->createPHPExcelObject($file);

        $this->infoSheet = $this->phpExcelObj->getSheet(0);

        $this->listSheet = $this->phpExcelObj->getSheet(1);

        $this->baseCategory = $this->em->getRepository('AppBundle:Categories')->findOneBy(['alias' => 'all']);

        $this->allFilter = $this->em->getRepository('AppBundle:Filters')->findOneBy(['name' => 'all']);

        foreach ($this->listSheet->getRowIterator(2) as $row) {
            $this->processProduct($row);
        }
    }

    /**
     * Process row
     *
     * @param \PHPExcel_Worksheet_Row $row
     */
    protected function processProduct(\PHPExcel_Worksheet_Row $row)
    {
        // If valid data
        if ($this->setCurrentRowData($row)) {
            $skuProduct = $this->em->getRepository('AppBundle:SkuProducts')
                ->findOneBySkuAndColorForVendor(
                    $this->getCurrentRowData('sku'),
                    $this->getCurrentRowData('color'),
                    $this->getCurrentRowData('decoration_color'),
                    $this->vendor
                );

            if (empty($skuProduct)) {
                $this->findOrInitSkuProduct();
            }
        }
    }

    /**
     * Find or init scu product
     *
     * @return \AppBundle\Entity\SkuProducts
     */
    private function findOrInitSkuProduct()
    {
        $product = $this->findOrInitProduct();
        // init new ProductModel
        $alias = $this->uniqAlias();
        $productModel = new \AppBundle\Entity\ProductModels;
        $productModel
            ->setProducts($product)
            ->setProductColors($this->getColor($this->getCurrentRowData('color')))
            ->setDecorationColor($this->getColor($this->getCurrentRowData('decoration_color')))
            ->setSizes($this->getCurrentRowData('sizes'))
            ->setPrice($this->getCurrentRowData('price'))
            ->setWholesalePrice($this->getCurrentRowData('wholesale_price'))
            ->setName($this->getCurrentRowData('model'))
            ->setAlias($alias)
            ->setStatus($this->publishFlag)
            ->setActive($this->publishFlag)
            ->setInStock($this->getCurrentRowData('count') > 0);

        $this->em->persist($productModel);

        // init new SkuProduct
        $skuProduct = new \AppBundle\Entity\SkuProducts;
        $skuProduct
            ->setSku($this->getCurrentRowData('sku'))
            ->setName($this->getCurrentRowData('model'))
            ->setVendors($this->vendor)
            ->setPrice($this->getCurrentRowData('price'))
            ->setWholesalePrice($this->getCurrentRowData('wholesale_price'))
            ->setQuantity($this->getCurrentRowData('count'))
            ->setStatus($this->publishFlag)
            ->setActive($this->publishFlag)
            ->setProductModels($productModel);

        $this->em->persist($skuProduct);
        $this->em->flush();
        return $skuProduct;
    }

    /**
     * Find or create product
     *
     * @return \AppBundle\Entity\Products
     */
    private function findOrInitProduct()
    {
        // Cut the Product Name for last digit.
        preg_match('/^(.*)\d+/', $this->getCurrentRowData('model'), $skuName);
        $skuName = (isset($skuName[0]))
            ? $skuName[0]
            : $this->getCurrentRowData('model');

        $product = $this->em->getRepository('AppBundle:Products')->findOneByImportName($skuName);

        if (!$product) {
            // init new Product
            $product = new \AppBundle\Entity\Products;
            $product
                ->setImportName($skuName)
                ->setActionLabels(
                    $this->em->getRepository('AppBundle:ActionLabels')
                        ->findOneByName('none')
                )
                ->setStatus($this->publishFlag)
                ->setActive($this->publishFlag);
            $this->em->persist($product);
        }

        // Attach categories
        $categoryName = $this->getCurrentRowData('type');

        $category = $this->em->getRepository('AppBundle:Categories')->findOrCreate([
            'name' => $categoryName,
            'parrent' => $this->baseCategory,
            'filters' => $this->allFilter,
            'alias' => $this->slugify->slugify($categoryName)
        ], ['active' => $this->publishFlag]);

        $product->setBaseCategory($category);

        // Attach characteristics to products and it categories
        foreach (['category', 'material', 'composition', 'parameters', 'season', 'packaging_contain'] as $characteristicCode) {
            $this->attachCharacteristics($product, $characteristicCode, $this->getCurrentRowData($characteristicCode));
            $this->attachCharacteristics($category, $characteristicCode, $this->getCurrentRowData($characteristicCode));
        }

        return $product;
    }

    /**
     * Process characteristics, if characteristics is array - process recursively
     *
     * @param $item
     * @param $characteristicCode
     * @param $characteristicValue
     */
    private function attachCharacteristics(CharacteristicableInterface $item, $characteristicCode, $characteristicValue)
    {
        if (is_array($characteristicValue)) {
            foreach ($characteristicValue as $characteristicValueItem) {
                $this->attachCharacteristics($item, $characteristicCode, $characteristicValueItem);
            }
            return;
        }

        $characteristic = $this->em->getRepository('AppBundle:Characteristics')
            ->findOrCreate(['name' => $this->characteristicNameByCode($characteristicCode)]);

        // If given item is Category
        if ($item instanceof Categories) {
            $item->addCharacteristic($characteristic);
        }

        if ($characteristicValue) {
            $characteristicValue = $this->em->getRepository('AppBundle:CharacteristicValues')->findOrCreate(
                ['characteristics' => $characteristic, 'name' => $characteristicValue],
                ['inFilter' => 1]
            );

            $item->addCharacteristicValue($characteristicValue);
            $this->em->persist($item);
        }
    }

    /**
     * Set current row data. If data not valid - return false
     *
     * @param $row
     * @return bool
     * @throws \PHPExcel_Exception
     */
    private function setCurrentRowData($row)
    {
        $this->currentRowData = [];

        // Set data from sheet with list
        foreach ($this->listColumnsMap as $letter => $key) {
            $this->currentRowData[$key] = $this->listSheet->getCell($letter . $row->getRowIndex())->getValue();
        }

        // Set data from info sheet
        if ($letter = $this->getInfoColumnByArticle()) {
            foreach ($this->infoRowsMap as $number => $key) {
                $this->currentRowData[$key] = $this->infoSheet->getCell($letter . $number)->getValue();
            }
        }

        if (count($this->validator->validate($this->currentRowData, $this->validationRules()))) {
            return false;
        }

        return true;
    }

    private function getInfoColumnByArticle()
    {
        // Iterate through all cells of article row
        foreach ($this->infoSheet->getRowIterator(self::ARTICLE_ROW)->current()->getCellIterator('B') as $cell) {
            if ($cell->getValue() == $this->getCurrentRowData('sku')) {
                return $cell->getColumn();
            }
        }
        return false;
    }

    /**
     * Get current row data
     *
     * @param $key
     * @return mixed
     */
    private function getCurrentRowData($key)
    {
        $data = isset($this->currentRowData[$key]) ? $this->currentRowData[$key] : '';

        $data = trim($data);

        // Remove repeated spaces
        $data = preg_replace('/\s+/', ' ', $data);

        $prepareMethod = 'prepare' . Str::studly($key);
        if (method_exists($this, $prepareMethod)) {
            $data = $this->$prepareMethod($data);
        }

        return $data;
    }

    /**
     * uniqAlias
     *
     * @return string
     */
    private function uniqAlias()
    {
        $slugify = new Slugify();
        $firstAlias = $slugify->slugify($this->getCurrentRowData('model'));
        $alias = $firstAlias;
        $counter = 1;
        while (!empty($this->em->getRepository('AppBundle:ProductModels')
            ->findOneByAlias($alias))
        ) {
            $alias = $firstAlias . $counter;
            $counter++;
        }

        return $alias;
    }

    /**
     * Import item validation rules
     *
     * @return Collection
     */
    protected function validationRules()
    {
        // To protect initializing many times
        if ($this->validationRules) return $this->validationRules;

        return $this->validationRules = new Collection([
            'sku' => new NotBlank(),
            'count' => new NotBlank(),
            'sizes' => new NotBlank(),
            'price' => new NotBlank(),
            'wholesale_price' => new NotBlank(),
            'color' => new NotBlank(),
            'decoration_color' => new Optional(),
            'model' => new NotBlank(),
            'type' => new NotBlank(),
            'category' => new Optional(),
            'material' => new Optional(),
            'composition' => new Optional(),
            'description' => new Optional(),
            'features' => new Optional(),
            'parameters' => new Optional(),
            'action' => new Optional(),
            'discount' => new Optional(),
            'sale' => new Optional(),
            'season' => new Optional(),
            'wholesale_min' => new Optional(),
            'packaging_contain' => new Optional(),
        ]);
    }

    /**
     * Get color
     *
     * @param $colorStr
     * @return mixed
     */
    private function getColor($colorStr)
    {
        return $colorStr ? $this->em->getRepository('AppBundle:ProductColors')->findOrCreate(['name' => $colorStr]) : null;
    }

    /**
     * Get sizes array, used for getCurrentRowData
     *
     * @param $sizes
     * @return mixed
     */
    private function prepareSizes($sizes)
    {
        $sizesArray = [];
        $sizes = array_map(function ($size) {
            return trim($size);
        }, explode('/', $sizes));

        foreach ($sizes as $sizeStr) {
            if ($sizeStr) {
                $sizesArray[] = $this->em->getRepository('AppBundle:ProductModelSizes')
                    ->findOrCreate(['size' => $sizeStr]);
            }
        }

        return $sizesArray;
    }

    /**
     * Return characteristic name from info sheet
     *
     * @param $code
     * @return mixed
     */
    private function characteristicNameByCode($code)
    {
        $row = array_search($code, $this->infoRowsMap);

        if ($row !== false) {
            return $this->infoSheet->getCell('A' . $row)->getValue();
        }

        return false;
    }

}
