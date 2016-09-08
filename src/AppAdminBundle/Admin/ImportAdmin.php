<?php

namespace AppAdminBundle\Admin;

use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\Products;
use AppBundle\FileReader\Transformer\PriceImportTransformer;
use AppBundle\FileReader\XlsReader;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
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
     * @var XlsReader
     */
    protected $reader;

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
     * Import
     *
     * @param $file
     * @param array $params
     */
    public function import($file, $params = [])
    {
        $this->reader = new XlsReader($file, new PriceImportTransformer());

        foreach ($this->reader as $row) {
            // If valid data
            if (count($this->validator->validate($row, $this->validationRules())) == 0) {
                switch (Arr::get($params, 'type')) {
                    case self::APPEND_TYPE:
                        $this->updateProduct($row);
                        break;
                    case self::ACTUALIZE_TYPE:
//                        Currently this not used
//                        $this->actualizeProduct($row);
                }
            }
        }
    }

    /**
     * @param $file
     * @param array $params
     * @return array
     */
    public function validateImport($file, $params = [])
    {
        $this->reader = new XlsReader($file, new PriceImportTransformer());
        $result = [
            'errors' => [],
            'total' => 0,
            'data' => $this->reader
        ];

        foreach ($this->reader as $key => $row) {
            $result['total']++;
            $errors = $this->validator->validate($row, $this->validationRules());
            if ($errors->count()) {
                $result['errors'][$key] = $errors;
            }
        }

        return $result;
    }

    /**
     * Process row
     *
     * @param integer array data
     */
    protected function updateProduct($data)
    {
        // Process product
        $product = $this->em->getRepository('AppBundle:Products')->findOneBy(['article' => $data['products.article']]);

        if (!$product) {
            $product = new Products();
            $product->setArticle($data['products.article'])
                ->setActive(true)
                ->setName($data['products.name'])
                ->setBaseCategory($this->getCategory($data['products.baseCategory.name']));

            $this->em->persist($product);
            $this->em->flush();
        }

        // Process product model
        $productModel = $this->updateOrCreateProductModel($product, $data);

        // Process product model sizes
        $this->updateOrCreateSpecificSize($productModel, $data);

        $this->em->flush();
    }

    /**
     * @param $data
     */
    protected function actualizeProduct($data)
    {
        $product = $this->em->getRepository('AppBundle:Products')->findOneBy(['article' => $data['products.article']]);

        $productModel = $this->em->getRepository('AppBundle:ProductModels')->getModelsByProductAndColors($product,
            $data['color.name'], $data['decorationColor.name']);

        if ($productModel) {
            $productModel->setQuantity($data['model.quantity']);

            $this->updateOrCreateSpecificSize($productModel, $data);

            $this->em->persist($productModel);
            $this->em->flush();
        }
    }

    /**
     * @param $product
     * @param $data
     * @return \AppBundle\Entity\Products
     */
    private function updateOrCreateProductModel($product, $data)
    {
        $model = $this->em->getRepository('AppBundle:ProductModels')->getModelsByProductAndColors($product,
            $data['color.name'], $data['decorationColor.name']);

        if (!$model) {
            $model = new ProductModels();
        }

        $model->setAlias($this->getUniqalizedAlias($data['model.alias']))
            ->setPublished(true)
            ->setProducts($product)
            ->setQuantity($data['model.quantity'])
            ->setPrice($data['model.price'])
            ->setWholesalePrice($data['model.wholesalePrice'])
            ->setProductColors($this->getColor($data['color.name']))
            ->setDecorationColor($this->getColor($data['decorationColor.name']));

        $this->em->persist($model);
        $this->em->flush();

        return $model;
    }

    /**
     * @param $productModel
     * @param $data
     * @return \AppBundle\Entity\Products
     */
    private function updateOrCreateSpecificSize($productModel, $data)
    {
        $specificSize = $this->em->getRepository('AppBundle:ProductModelSpecificSize')
            ->findOneByProductModelAndSizeName($productModel, $data['size']);

        if (!$specificSize) {
            $size = $this->em->getRepository('AppBundle:ProductModelSizes')->findOrCreate(['size' => $data['size']]);

            $specificSize = new ProductModelSpecificSize();
            $specificSize->setSize($size);
        }
        $specificSize->setQuantity($data['quantity'])
            ->setPrice($data['price'])
            ->setWholesalePrice($data['wholesalePrice'])
            ->setPreOrderFlag($data['preOrderFlag'])
            ->setModel($productModel);

        $this->em->persist($specificSize);

        return $specificSize;
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
            'products.name' => new NotBlank(),
            'products.article' => new NotBlank(),
            'products.price' => new NotBlank(),
            'products.wholesalePrice' => new NotBlank(),
            'color.name' => new NotBlank(),
            'model.alias' => new NotBlank(),
            'model.price' => new NotBlank(),
            'model.wholesalePrice' => new NotBlank(),
            'size' => new NotBlank(),
            'price' => new NotBlank(),
            'wholesalePrice' => new NotBlank(),
            'quantity' => new NotBlank(),

            'preOrderFlag' => new Optional(),
            'model.quantity' => new Optional(),
            'products.active' => new Optional(),
            'model.published' => new Optional(),
            'products.baseCategory.name' => new Optional(),
            'decorationColor.name' => new Optional(),
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
     * Get color
     *
     * @param $alias
     * @return mixed
     */
    private function getUniqalizedAlias($alias)
    {
        if ($this->em->getRepository('AppBundle:ProductModels')->findOneByAlias($alias)) {
            return $alias . '-' . uniqid();
        }

        return $alias;
    }

    /**
     * Get color
     *
     * @param $categoryName
     * @return mixed
     */
    private function getCategory($categoryName)
    {
        if ($category = $this->em->getRepository('AppBundle:Categories')->findOneBy(['name' => $categoryName])) {
            return $category;
        }

        if ($this->baseCategory) {
            return $this->baseCategory;
        }

        $this->baseCategory = $this->em->getRepository('AppBundle:Categories')->findOneBy(['alias' => 'all']);

        return $this->baseCategory;
    }

}
