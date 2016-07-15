<?php

namespace AppAdminBundle\Admin;

use AppAdminBundle\Transformer\ImportTransformer;
use AppBundle\Entity\Categories;
use AppBundle\Entity\CharacteristicableInterface;
use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\Products;
use AppBundle\Entity\Vendors;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImportAdmin
{
    const ACTUALIZE_TYPE = 0;

    const APPEND_TYPE = 1;

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
        $this->transformer = new ImportTransformer();
    }

    /**
     * Import
     *
     * @param $file
     * @param array $params
     */
    public function import($file, $params = [])
    {
        $this->phpExcelObj = $this->container->get('phpexcel')
            ->createPHPExcelObject($file);

        $this->infoSheet = $this->phpExcelObj->getSheet(0);

        $this->listSheet = $this->phpExcelObj->getSheet(1);

        $this->baseCategory = $this->em->getRepository('AppBundle:Categories')->findOneBy(['alias' => 'all']);

        $this->allFilter = $this->em->getRepository('AppBundle:Filters')->findOneBy(['name' => 'all']);

        foreach ($this->listSheet->getRowIterator(2) as $row) {
            // If valid data
            if ($this->setCurrentRowData($row)) {
                $this->processProduct(Arr::get($params, 'type'));
            }
        }
    }

    /**
     * Process row
     *
     * @param integer $type
     */
    protected function processProduct($type)
    {
        // Process product
        $product = $this->em->getRepository('AppBundle:Products')
            ->findOneBy(['article' => $this->getCurrentRowData('sku')]);

        if ($type == self::APPEND_TYPE) {
            if (!$product) {
                $product = $this->createProduct();
            }
            // Process product model
            $productModel = $this->em->getRepository('AppBundle:ProductModels')->getModelsByProductAndColors($product,
                $this->getCurrentRowData('color'), $this->getCurrentRowData('decoration_color'));

            if (!$productModel) {
                $productModel = $this->createProductModel($product);
            }
            // Process product model sizes
            foreach ($this->getCurrentRowData('sizes') as $size) {
                $specificSize = $this->em->getRepository('AppBundle:ProductModelSpecificSize')
                    ->findOneByProductModelAndSizeName($productModel, $size);
                if (!$specificSize) {
                    $this->createProductModelSpecificSize($productModel, $size);
                }
            }

        } elseif ($type == self::ACTUALIZE_TYPE) {
            if ($product) {
                $this->updateProduct($product);
            }
        }

        $this->em->flush();
    }

    /**
     * Find or init scu product
     *
     * @return \AppBundle\Entity\Products
     */
    private function createProduct()
    {
        $product = new Products();
        $product->setArticle($this->getCurrentRowData('sku'))
            ->setQuantity($this->getCurrentRowData('count'))
            ->setActive(true)
            ->setName($this->getCurrentRowData('model'))
            ->setBaseCategory($this->baseCategory);

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    /**
     * @param $product
     * @return \AppBundle\Entity\Products
     */
    private function createProductModel($product)
    {
        $model = new ProductModels();
        $model->setAlias($this->uniqAlias())
            ->setPublished(true)
            ->setProducts($product)
            ->setQuantity($this->getCurrentRowData('count'))
            ->setPrice($this->getCurrentRowData('price'))
            ->setWholesalePrice($this->getCurrentRowData('wholesale_price'))
            ->setProductColors($this->getColor($this->getCurrentRowData('color')))
            ->setDecorationColor($this->getColor($this->getCurrentRowData('decoration_color')));

        $this->em->persist($model);
        $this->em->flush();

        return $model;
    }

    /**
     * @param $productModel
     * @param $sizeName
     * @return \AppBundle\Entity\Products
     */
    private function createProductModelSpecificSize($productModel, $sizeName)
    {
        $size = $this->em->getRepository('AppBundle:ProductModelSizes')->findOrCreate(['size' => $sizeName]);

        $specificSize = new ProductModelSpecificSize();
        $specificSize->setSize($size)
            ->setQuantity($this->getCurrentRowData('count'))
            ->setModel($productModel);

        $this->container->get('logger')->info("Add $sizeName");

        $this->em->persist($specificSize);
        $this->em->flush();

        return $specificSize;
    }

    /**
     * Update quantity and sizes
     * @param Products $product
     * @return \AppBundle\Entity\Products
     */
    private function updateProduct(Products $product)
    {
        $productModel = $this->em->getRepository('AppBundle:ProductModels')->getModelsByProductAndColors($product,
            $this->getCurrentRowData('color'), $this->getCurrentRowData('decoration_color'));

        if ($productModel) {
            $productModel->setQuantity($this->getCurrentRowData('count'));

            foreach ($this->getCurrentRowData('sizes') as $size) {
                $specificSize = $this->em->getRepository('AppBundle:ProductModelSpecificSize')
                    ->findOneByProductModelAndSizeName($productModel, $size);

                if (!$specificSize) {
                    $this->createProductModelSpecificSize($productModel, $size);
                } else {
                    $specificSize->setQuantity($this->getCurrentRowData('count'));
                    $this->em->persist($specificSize);
                }
            }
            $this->em->persist($productModel);
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
        $currentRowData = [];

        // Set data from sheet with list
        foreach ($this->listColumnsMap as $letter => $key) {
            $currentRowData[$key] = $this->listSheet->getCell($letter . $row->getRowIndex())->getValue();
        }

        $this->transformer->setData($currentRowData);

        // Set data from info sheet
        if ($letter = $this->getInfoColumnLetterByArticle()) {
            foreach ($this->infoRowsMap as $number => $key) {
                $currentRowData[$key] = $this->infoSheet->getCell($letter . $number)->getValue();
            }
        }

        if (count($this->validator->validate($currentRowData, $this->validationRules()))) {
            return false;
        }

        $this->transformer->setData($currentRowData);

        return true;
    }

    /**
     * @return mixed
     */
    private function getInfoColumnLetterByArticle()
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
        return $this->transformer->transformedData()[$key];
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
        if ($this->validationRules) {
            return $this->validationRules;
        }

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

}
