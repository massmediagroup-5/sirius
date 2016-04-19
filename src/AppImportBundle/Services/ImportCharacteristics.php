<?php

namespace AppImportBundle\Services;

use Doctrine\ORM\EntityManager;
use Cocur\Slugify\Slugify;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class: ImportCharacteristics
 *
 */
class ImportCharacteristics
{

    /**
     * serviceContainer
     *
     * @var mixed
     */
    protected $serviceContainer = null;

    /**
     * fs
     *
     * @var mixed
     */
    private $fs = null;

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * logger
     *
     * @var mixed
     */
    private $logger;

    /**
     * parsingEm
     *
     * @var mixed
     */
    private $parsingEm;

    /**
     * methodData
     *
     * @var array
     */
    private $methodData = array();

    /**
     * putMethodData
     *
     * @var array
     */
    private $putMethodData = array();

    /**
     * tableName
     *
     * @var array
     */
    private $tableName = array();

    /**
     * category
     *
     * @var mixed
     */
    private $category;

    /**
     * columns
     *
     * @var mixed
     */
    private $columns = array();

    /**
     * importItemData
     *
     * @var array
     */
    private $importItemData = array();

    /**
     * spec
     *
     * @var array
     */
    private $spec = array();

    /**
     * specGroups
     *
     * @var array
     */
    private $specGroups = array();

    /**
     * specNames
     *
     * @var array
     */
    private $specNames = array();

    /**
     * mainCategory
     *
     * @var mixed
     */
    private $mainCategory;

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
        $this->fs = new Filesystem();
        $this->serviceContainer = $container;
        $this->logger = $container->get('logger');
        $this->em = $em;
        $this->parsingEm = $this->serviceContainer->get('doctrine')
            ->getManager('parsing')
            ->getConnection()
            ;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->parsingEm->getConfiguration()->setSQLLogger(null);
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
        preg_match('/^setTabel(.*)/', $name, $table);
        preg_match('/^setCategory/', $name, $category);
        preg_match('/^setColumn(.*)/', $name, $column);
        preg_match('/^set(.*)/', $name, $method);
        preg_match('/^put(.*)/', $name, $put);
        if (!empty($table)) {
            $this->tableName[$table[1]] = $value[0];
        } elseif (!empty($category)) {
            $this->category = $value[0];
        } elseif (!empty($column)) {
            $this->columns[$method[1]] = $value[0];
        } elseif (!empty($method)) {
            $this->methodData[$method[1]] = $value[0];
        } elseif (!empty($put)) {
            $this->putMethodData[$put[1]] = $value[0];
        }
        return $this;
    }

    /**
     * import
     *
     * @return this
     */
    public function import()
    {
        $characteristicTabel = $this->tableName['Characteristics'];
        $valTabel = $this->tableName['CharacteristicValues'];
        $idColumn = $this->methodData['Numerator'];
        $modelNameChV = $this->methodData['ModelNameInChV'];
        $modelNameM = $this->methodData['ModelNameInModel'];
        $notCharacteristics = $this->methodData['NotCharacteristics'];

        $this->selectAllSpecs();
        // Create SpecNames for all CharacteristicValue for Products.
        foreach ($this->spec as $key => $value) {
            $this->specNames[$value['spec']] = $value['spec_on_site'];
        }
        $query = $this->parsingEm->prepare(
            '
                SELECT
                    count(*) as count
                FROM `'. $valTabel .'` as characVal
                INNER JOIN
                    `'. $this->tableName['Models'] .'` models
                    ON
                        characVal.'. $modelNameChV .' =
                            models.'. $modelNameM .'
            ');
        $query->execute();
        $row = $query->fetchAll();
        // ProgressBar
        $this->output->writeln('<info>Start import CharacteristicValues for models</info>');
        $cycleCount = isset($row[0]['count']) ? (int)$row[0]['count'] : 0;
        $progress = new ProgressBar($this->output, $cycleCount);
        $progress->setFormat('debug');
        $progress->start();
        $i = $id = 0;
        do {
            ++$i;
            $query = $this->parsingEm->prepare(
                '
                    SELECT
                        characVal.*,
                        models.'.(string)$this->methodData['ModelArticulInModel'].',
                        models.'.(string)$this->methodData['ModelNameInModel'].'
                    FROM `'. $valTabel .'` as characVal
                    INNER JOIN
                        `'. $this->tableName['Models'] .'` models
                        ON
                            characVal.'. $modelNameChV .' =
                                models.'. $modelNameM .'
                    WHERE
                        characVal.id > '.$id.'
                    ORDER BY characVal.id
                    LIMIT 20
                ');
            $query->execute();
            $rows = $query->fetchAll();
            // If parsing Was success:
            // All SELECTed id of CharacteristicValues tabels.
            // We need to delete it after importing.
            $ids = [];
            foreach ($rows as $row) {
                if (isset($row[$idColumn])) {
                    if ($id === 0) {
                        $this->updateOrInsertCharacteristics($row);
                    }
                    $id = $row[$idColumn];
                    $ids[] = $id;
                    if (!empty($row[$notCharacteristics])) {
                        $row = $this->rowTrim($row);
                        $this->updateOrInsertCharacValues($row);
                    }
                }
                $progress->advance();
            }
            // Deleting from `mytex.test` table
            if (!empty($ids)) {
            $ids = implode(',', $ids);
                $query = $this->parsingEm->prepare(
                    '
                        DELETE
                        FROM `'. $valTabel .'`
                        WHERE
                            `'.$idColumn.'` IN ('.$ids.')
                    ');
                $query->execute();
            }
            $this->em->clear();
        } while (!empty($rows) && (($cycleCount - $i) > 0));
        $progress->finish();
        $this->output->writeln("\n");

        return $cycleCount;
    }

    /**
     * selectAllSpecs
     *
     */
    private function selectAllSpecs()
    {
        // Select all spec names
        $query = $this->parsingEm->prepare(
            '
                SELECT *
                FROM `'. $this->tableName['Characteristics'].'`
                    as characteristics
            ');
        $query->execute();
        $this->spec = $query->fetchAll();
        //
        // Select all spec groups
        $query = $this->parsingEm->prepare(
            '
                SELECT *
                FROM `'. $this->tableName['CharacteristicsGroups'].'`
                    as characteristicsGroups
            ');
        $query->execute();
        $this->specGroups = $query->fetchAll();
    }

    /**
     * updateOrInsertCharacValues
     *
     * @param mixed $row
     */
    private function updateOrInsertCharacValues($row)
    {
        // Get product for connecting
        $skuProduct = $this->em
            ->getRepository('AppBundle:SkuProducts')
            ->findOneBySku($row[$this->methodData['ModelArticulInModel']])
            ;
        if (!empty($skuProduct)) {
            if ($this->validateSkuProduct($skuProduct, $row)) {
                $productModels = $skuProduct->getProductModels();
                $this->deleteFromNotParsed($row);
            } else {
                $this->insertOrUpdateNotParsed($row);
            }
        }
        if (!empty($productModels)) {
            $product = $productModels->getProducts();
            $color = $this->selectColor($row);
            // Get cureent column (Characteristics) for importing SKU
            $charColums = $this->getColumsForCurrentRow($row);
            foreach ($row as $column => $value) {
                if ((!empty($value)) && (in_array($column, $charColums) || ($column === $this->category))) {
                    $characteristicName = $this->getCharacteristicName($column);
                    $characteristic = $this->em
                        ->getRepository('AppBundle:Characteristics')
                        ->findOneByName($characteristicName)
                        ;
                    $values = $this->getCharacteristicValuesArray($value);
                    foreach ($values as $value) {
                        $characteristicValue = $this->em
                            ->getRepository('AppBundle:CharacteristicValues')
                            ->getValuesByCharacteristic($characteristicName, $value);
                        if (empty($characteristicValue)) {
                            $characteristicValue =
                                new \AppBundle\Entity\CharacteristicValues;
                            $characteristicValue
                                ->setName($value)
                                ->setCharacteristics($characteristic)
                                ;
                            $this->em->persist($characteristicValue);
                        }
                        // Update Product
                        $characteristicValues = $product->getCharacteristicValues();
                        // If product already have value of characteristic we do nothing.
                        $valueExist = false;
                        foreach ($characteristicValues as $chValue) {
                            if ($characteristicValue->getName() === $chValue->getName()) {
                                $valueExist = true;
                            }
                        }
                        if (!$valueExist)
                            $product->addCharacteristicValue($characteristicValue);
                        // Set active our Product.
                        $product
                            ->setStatus(1)
                            ->setActive(1)
                            ;
                        $this->em->persist($product);
                        $productModels
                            ->setProductColors($color)
                            ->setStatus(1)
                            ->setActive(1)
                            ;
                        $this->em->persist($productModels);
                        $skuProduct
                            ->setStatus(1)
                            ->setActive(1)
                            ;
                        $this->em->persist($skuProduct);
                    }
                }
            }
            // Update Category
            // Only when we want to do so.
            $categoryName = $row[$this->category];
            if ($this->methodData['PersistCategories']) {
                $category = $this->selectOrInsertCategory($categoryName);
                $category = $this->updateCategory($product, $category);
            }
            // Parse Img
            $this->parseImg($row, $productModels);
        }
        $this->em->flush();
        $this->em->clear();
        return null;
    }

    /**
     * updateOrInsertCharacteristics
     *
     * By default new Characteristics will be added with setInFilter = 0.
     *
     * @param mixed $row
     */
    private function updateOrInsertCharacteristics($row)
    {
        foreach ($row as $column => $value) {
            if (!in_array($column, $this->methodData)) {
                $characteristicName = $this->getCharacteristicName($column);
                $characteristic = $this->em
                    ->getRepository('AppBundle:Characteristics')
                    ->findOneByName($characteristicName);
                if (empty($characteristic)) {
                    $characteristic = new \AppBundle\Entity\Characteristics;
                    $characteristic->setName($characteristicName)
                        ->setInFilter(0)
                        ;
                    $this->em->persist($characteristic);
                }
            }
        }
        $this->em->flush();
    }

    /**
     * selectOrInsertCategory
     *
     * @param mixed $categoryName
     *
     * @return void
     */
    private function selectOrInsertCategory($categoryName)
    {
        $slugify = new Slugify();
        $category = $this->em
            ->getRepository('AppBundle:Categories')
            ->findOneByName($categoryName);
        if (empty($category)) {
            $this->mainCategory = $this->em
                ->getRepository('AppBundle:Categories')
                ->findOneById(1); 
            $category = new \AppBundle\Entity\Categories;
            $category->setName($categoryName)
                ->setAlias($slugify->slugify($categoryName))
                ->setParrent($this->mainCategory)
                ->setActive(0)
                ->setInMenu(0)
                ;
            $this->em->persist($category);
        }
        return $category;
    }

    /**
     * updateCategory
     *
     * @param \AppBundle\Entity\Products $product
     * @param \AppBundle\Entity\Categories $category
     *
     * @return void
     */
    private function updateCategory(
        \AppBundle\Entity\Products    $product,
        \AppBundle\Entity\Categories  $category
    )
    {
        // Find, is current category have this product.
        $productCategory = $this->em
            ->getRepository('AppBundle:Categories')
            ->findCategory($product->getId(), $category->getId());
        // If our product is in current category, we do nothing.
        if (empty($productCategory)) {
            $product->addCategory($category);
            $this->em->merge($product);
        }
        //
        // Find, is current category are baseCategory fore our product.
        $product->setBaseCategory($category);
        $this->em->persist($product);

        return null;
    }

    /**
     * getCharacteristicName
     *
     * @param mixed $spec
     *
     * @return string
     */
    private function getCharacteristicName($spec)
    {
        $characteristicName = isset($this->specNames[$spec])
            ? $this->specNames[$spec]
            : $spec;

        return $characteristicName;
    }

    /**
     * validateSkuProduct
     *
     * @param mixed $skuProduct
     * @param mixed $row
     *
     * @return boolean
     */
    private function validateSkuProduct(&$skuProduct, $row)
    {
        $brand = $row[$this->putMethodData['BrandNameInChV']];
        $modelName = $row[$this->methodData['ModelNameInChV']];
        $result = mb_stripos($modelName, $brand);

        return ($result === false) ? false : true;
    }

    /**
     * insertOrUpdateNotParsed
     *
     * @param mixed $row
     * @param mixed $updateModels
     *
     * @return null
     */
    public function insertOrUpdateNotParsed($row, $updateModels = false)
    {
        $row = $this->rowTrim($row);
        $modelName = $this->methodData['ModelNameInModel']; // model
        $modelArticul = $this->methodData['ModelArticulInModel']; // articul
        $query = $this->parsingEm->prepare(
            '
                INSERT INTO `'. $this->tableName['NotParsed'].'`
                (
                    `'. $modelName.'`,
                    `'. $modelArticul.'`,
                    `count`
                )
                VALUES (
                    "'. $row[$modelName] .'",
                    "'. $row[$modelArticul] .'",
                    1
                )
                ON DUPLICATE KEY UPDATE
                    `count` = `count` + 1
            ');
        $query->execute();
        if ($updateModels) {
            $query = $this->parsingEm->prepare(
                '
                    INSERT IGNORE
                    INTO `'. $this->tableName['Models'].'`
                    (
                        `'. $modelName.'`,
                        `'. $modelArticul.'`
                    )
                    VALUES (
                        "'. $row[$modelName] .'",
                        "'. $row[$modelArticul] .'"
                    )
                ');
            $query->execute();
        }
        return null;
    }

    /**
     * deleteFromNotParsed
     *
     * @param mixed $row
     *
     * @return null
     */
    private function deleteFromNotParsed($row)
    {
        $modelName = $this->methodData['ModelNameInModel']; // model
        $modelArticul = $this->methodData['ModelArticulInModel']; // articul
        $query = $this->parsingEm->prepare(
            '
                DELETE FROM
                    `'. $this->tableName['NotParsed'] .'`
                WHERE
                    `'. $modelName.'` = "'.$row[$modelName].'"
                AND
                    `'. $modelArticul.'` = "'.$row[$modelArticul].'"
            ');
        $query->execute();
        return null;
    }
    

    /**
     * selectColor
     *
     * @param mixed $row
     *
     * @return \AppBundle\Entity\ProductColors
     */
    private function selectColor($row)
    {
        $pattern = '/^[^(\ \d,\+\/;)]+/';
        preg_match($pattern, $row[$this->columns['ColumnColor']], $color);
        $color = (isset($color[0])) ? $color[0] : 'none';
        $colorObj = $this->em->getRepository('AppBundle:ProductColors')
            ->findOneByName($color);
        $colorObj = $colorObj ? $colorObj : new \AppBundle\Entity\ProductColors;
        $colorObj
            ->setName($color)
            ->setHex('#fffeee')
            ;
        $this->em->persist($colorObj);
        //dump($color);
        return $colorObj;
    }

    /**
     * parseImg
     *
     * @param mixed $row
     * @param mixed $model
     */
    private function parseImg($row, $model)
    {
        $allow = 'jpg|jpeg|png';
        $dir = $this->serviceContainer->get('kernel')->getRootDir();
        clearstatcache(true);
        $imgPath = realpath($dir . '/..') . '/web/img/products/';
        $urlRow = $row[$this->methodData['ImgUrl']];
        $imgUrls = explode(',', $urlRow);
        foreach ($imgUrls as $imgUrl) {
            $thumbnail = sha1($imgUrl);
            // Didn't parse img if we have one
            try {
                $imgObj = $this->em
                    ->getRepository('AppBundle:ProductModelImages')
                    ->findOneByLinkForModel($imgUrl, $model->getId());
            } catch (\Doctrine\Orm\NoResultException $e) {
                $imgObj = new \AppBundle\Entity\ProductModelImages;
                if (preg_match("/\.{$allow}$/i", $imgUrl, $ext)) {
                    $thumbnail .= $ext[0];
                    try {
                        $this->fs->copy($imgUrl, $imgPath . $thumbnail);
                        $imgObj
                            ->setProductModels($model)
                            ->setLink($imgUrl)
                            ->setThumbnail($thumbnail)
                            ;
                        $this->em->persist($imgObj);
                    } catch (IOExceptionInterface $e) {
                        $this->logger("Can't download: " . $e->getPath());
                        $this->insertOrUpdateNotParsed($row);
                    }
                }
            }
        }
        return null;
    }

    /**
     * getCharacteristicValuesArray
     *
     * @param mixed $celValue
     *
     * @return array
     */
    private function getCharacteristicValuesArray($celValue)
    {
        $values = explode('_', $celValue);
        // --temporary
        foreach ($values as $key => $value) {
            $length = (mb_strlen($value) > 255) ? 255 : mb_strlen($value);
            $values[$key] = mb_substr($value, 0, $length);
        }
        // ------
        return $values;
    }

    /**
     * getColumsForCurrentRow
     *
     * Get columns name from `specnames`
     *
     * @param mixed $row
     *
     * @return array
     */
    private function getColumsForCurrentRow($row)
    {
        $columns = explode('_', $row[$this->methodData['NotCharacteristics']]);
        $columnsNames = [];
        foreach ($columns as $column) {
            $column = trim($column);
            // First search name of characteristic in Groups of Characteristics
            $groupKey = array_search($column, array_column($this->specGroups, 'spec_in_speclist'));
            $group = ($groupKey !== false) ? $this->specGroups[$groupKey]['spec_group'] : false;
            // If we have found characteristic name in group of characteristics
            // we want to find the group name in specNames.
            $column = $group ? $group : $column;
            $columnsNames[] = array_search($column, $this->specNames);
        }
        return (array) $columnsNames;
    }

    /**
     * rowTrim
     *
     * @param mixed $row
     *
     * @return mixed
     */
    private function rowTrim($row)
    {
        foreach ($row as $key => $cell) {
            $cell = preg_replace('/\s+/', ' ', $cell);
            $row[$key] = trim($cell);
        }
        return $row;
    }

}
