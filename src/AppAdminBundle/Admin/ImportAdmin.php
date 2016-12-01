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
     * @throws \Exception
     */
    public function import($file, $params = [])
    {
        $this->reader = new XlsReader($file, new PriceImportTransformer());

        $this->em->getConnection()->beginTransaction();

        try {
            // Set to zero all quantity
            if (Arr::get($params, 'type') == self::ACTUALIZE_TYPE) {
                $this->em->createQueryBuilder()
                    ->update('AppBundle\Entity\ProductModelSpecificSize', 's')
                    ->set('s.quantity', '?1')
                    ->setParameter(1, 0)
                    ->getQuery()
                    ->execute();
            }

            foreach ($this->reader as $row) {
                // If valid data
                if (count($this->validator->validate($row, $this->validationRules())) == 0) {
                    $this->updateSize($row, Arr::get($params, 'type'));
                }
            }

            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
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
    protected function updateSize($data, $importType)
    {
        $specificSize = $this->em->getRepository('AppBundle:ProductModelSpecificSize')
            ->findOneByProductArticleColorAndSizeName(
                $data['products.article'],
                $data['color.name'],
                $data['decorationColor.name'],
                $data['size']
            );

        if (!$specificSize) {
            $model = $this->em->getRepository('AppBundle:ProductModels')->getModelsByProductArticleAndColors(
                $data['products.article'],
                $data['color.name'],
                $data['decorationColor.name']
            );
            if (!$model) {
                return;
            }
            $size = $this->em->getRepository('AppBundle:ProductModelSizes')->findOrCreate(['size' => $data['size']]);

            $specificSize = new ProductModelSpecificSize();
            $specificSize
                ->setSize($size)
                ->setModel($model);
        }

        switch ($importType) {
            case self::APPEND_TYPE:
                $specificSize->incrementQuantity($data['quantity']);
                break;
            case self::ACTUALIZE_TYPE:
                $specificSize->setQuantity($data['quantity']);
        }

        $specificSize
            ->setPrice($data['price'])
            ->setWholesalePrice($data['wholesalePrice'])
            ->setPreOrderFlag($data['preOrderFlag']);

        $this->em->persist($specificSize);

        $this->em->flush();
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
            'products.baseCategory.name' => new Optional(),
            'products.name' => new Optional(),
            'color.name' => new NotBlank(),
            'decorationColor.name' => new Optional(),
            'products.article' => new NotBlank(),
            'size' => new NotBlank(),
            'price' => new NotBlank(),
            'wholesalePrice' => new NotBlank(),
            'preOrderFlag' => new Optional(),
            'quantity' => new NotBlank(),
            'model.quantity' => new Optional(),
            'products.active' => new Optional(),
            'model.published' => new Optional(),
        ]);
    }

}
