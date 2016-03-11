<?php

namespace AppBundle\Entity\Repository;
use AppBundle\Entity\Characteristics;

/**
 * CharacteristicsRepository
 *
 */
class CharacteristicsRepository extends BaseRepository
{

    /**
     * query_obj
     *
     * @var mixed
     */
    private $query_obj;

    /**
     * __construct
     *
     * @param mixed $em
     * @param \Doctrine\ORM\Mapping\ClassMetadata $class
     */
    public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->query_obj = $this->createQueryBuilder('characteristics')
            ->select('characteristics');
        // Add a starting Joins.
        $this->query_obj
            ->innerJoin('characteristics.characteristicValues', 'characteristicValues')->addselect('characteristicValues')
            ->innerJoin('characteristicValues.products', 'products')->addselect('products');
    }

    /**
     * addWhere
     *
     * @param array $where
     *
     * $where - is array with table and columns structure.
     * We can define such keys:
     * - categories;
     * - products;
     * - productModels;
     * - productModelImages;
     * - productColors;
     * - actionLabels.
     *
     * Example:
     *  $where = array(
     *    // category - is structure for `categories` table.
     *    'categories' => array(
     *       'id'         => 1,
     *       'active'     => 1,
     *       ...
     *       'alias'      => 'some-alias'
     *    ),
     *    ...
     *    'products' => array(
     *        'active'    => 1,
     *        'published' => 1,
     *    )
     *  );
     *
     * @return this
     */
    public function addWhere(array $where = array())
    {
        // construct WHERE conditions
        foreach ($where as $whereKey => $whereValue) {
            foreach ($whereValue as $columnKey => $columnValue) {
                $this->query_obj
                    ->andWhere("{$table}.{$columnKey} = :{$columnKey}")
                    ->setParameter($columnKey, $columnValue);
            }
        }
        return $this;
    }

    /**
     * getAllCharacteristicsByCategory
     *
     * Get all characteristics with characteristicValues,
     * but only for those products that fit the category
     *
     * @param array $category
     *
     * $category - is array with columns structure for `categories` table.
     * Example:
     * $category = array(
     *    'id'    => 1,
     *    ...
     *    'alias' => 'some-alias'
     * );
     *
     * @param mixed $inFilter
     *
     * $inFilter may be:
     * 1 - search only those Characteristics that are in the filter
     * 0 - search only those Characteristics that are NOT in the filter
     * null (not set) - search ALL Characteristics
     *
     * @return mixed
     */
    public function getAllCharacteristicsByCategory(
        array   $category,
        $inFilter = null,
        $products_obj
    )
    {
        $query_obj = $this->query_obj
            ->innerJoin('products.categories', 'categories')->addselect('categories')
            ->innerJoin('products.baseCategory', 'baseCategory')->addselect('baseCategory')
            ->innerJoin('categories.filters', 'filters')
            ->innerJoin('filters.characteristics', 'characteristics2', 'WITH', 'characteristics.id = characteristics2.id')
            //->addSelect('
//(SELECT
            //COUNT (products1.id)
//FROM
            //AppBundle\Entity\Products products1
//INNER JOIN
            //products1.categories categories1
//INNER JOIN
            //products1.actionLabels actionLabels
//INNER JOIN
            //products1.productModels productModels
//INNER JOIN
            //productModels.productColors productColors
//INNER JOIN
            //productModels.skuProducts skuProducts
//LEFT JOIN
            //productModels.productModelImages productModelImages
//INNER JOIN
            //products1.productsBaseCategories productsBaseCategories
//INNER JOIN
            //productsBaseCategories.categories baseCategories
//WHERE
            //categories.alias = :alias AND categories.active = :active AND products1.active = :active AND products1.published = :published AND productModels.active = :active AND productModels.published = :published AND productModels.price >= :price_from AND productModels.price <= :price_to
//ORDER BY
            //productModels.priority ASC) as counte')
//->setParameter('active', 1)
//->setParameter('published', 1)
//->setParameter('price_from', 1)
//->setParameter('price_to', 10000)
            //->addSelect('(' . (string)$dql. ') as counte')
            //->addSelect($this->query_obj->expr()->count('DISTINCT characteristics.id') . ' as charcount')
            ->addSelect('
                (
                    SELECT COUNT(dctrn.id)
                    FROM \AppBundle\Entity\Products as dctrn
                    WHERE dctrn.id IN (' . (string)$products_obj . ')
                    OR dctrn.id = characteristicValues.id
                ) as counte
            ')
            //->addSelect('(SELECT COUNT (' . (string)$products_obj. ') as idd) as counte')
            //->addSelect([>$this->query_obj->expr()->count($products_obj)<] '('.$products_obj.') as counte')
        ;
        foreach ($products_obj->getParameters() as $parametr) {
            $query_obj->setParameter($parametr->getName(), $parametr->getValue());
        }

        // construct WHERE conditions
        if (isset($inFilter)) {
            $query_obj->where("characteristics.inFilter = :inFilter")
                ->setParameter('inFilter', $inFilter);
        }
        foreach ($category as $categoryColumnKey => $categoryColumnValue) {
            $query_obj
                ->andWhere("categories.{$categoryColumnKey} = :{$categoryColumnKey}")
                ->setParameter($categoryColumnKey, $categoryColumnValue);
        }
        $query_obj->addOrderBy('characteristics.name', 'ASC')
            ->addOrderBy('characteristicValues.name', 'ASC');

        return $query_obj->getQuery()->getResult();
    }

    /**
     * joinFilters
     *
     * Add JOIN with characteristicValues
     *
     * @param mixed $skuProduct
     * @param mixed $vendor
     *
     * @return mixed
     */
    public function joinSku(
        $skuProduct,
        $vendor = null
    )
    {
        $this->query_obj
            ->innerJoin('products.productModels', 'productModels')->addselect('productModels')
            ->innerJoin('productModels.skuProducts', 'skuProducts')->addselect('skuProducts')
            ->andWhere("skuProducts.sku = :skuProduct")->setParameter('skuProduct', $skuProduct);
        if (!is_null($vendor))
            $this->query_obj
                ->innerJoin('skuProducts.vendors', 'vendors')->addselect('vendors')
                ->andWhere("vendors.name = :vendor")
                ->setParameter('vendor', $vendor);
        return $this;
    }

    /**
     * getAllCharacteristicsByProduct
     *
     * @param array $skuProduct
     *
     * $product - is array with columns structure for `categories` table.
     * Example:
     * $product = array(
     *    'id'    => 1,
     *    ...
     *    'alias' => 'some-alias'
     * );
     *
     * @return mixed
     */
    public function getAllCharacteristicsByProductSku($skuProduct)
    {
        $query_obj = $this->createQueryBuilder('characteristics')
            ->select('characteristics')
            ->innerJoin('characteristics.characteristicValues', 'characteristicValues')->addselect('characteristicValues')
            ->innerJoin('characteristicValues.products', 'products')->addselect('products')
            ->innerJoin('products.productModels', 'productModels')->addselect('productModels')
            ->innerJoin('productModels.skuProducts', 'skuProducts')->addselect('skuProducts')
            ->where("skuProducts.sku = :skuProduct")->setParameter('skuProduct', $skuProduct);
        return $query_obj->getQuery()->getResult();
    }

    /**
     * getUnicCharacteristicsByProductInCategory
     *
     * We must select only those Characteristics that defined in ALL Products
     * for current Category.
     * Do not use "Categories<=>CharacteristicValues" relationship.
     *
     * @param \AppBundle\Entity\Categories $category
     *
     * @return mixed
     */
    public function getUnicCharacteristicsByProductInCategory(
        \AppBundle\Entity\Categories $category
    )
    {
        $query_obj = $this->createQueryBuilder('characteristics')
            ->select('characteristics')
            ->innerJoin('characteristics.characteristicValues', 'characteristicValues')->addSelect('characteristicValues')
            ->innerJoin('characteristicValues.products', 'products')->addSelect('products')
            ->innerJoin('products.categories', 'categories')->addSelect('categories')
            ->where("categories.id = :category")->setParameter('category', $category->getId())
            ->groupBy('characteristics.id')
            ->having("count(characteristics.id) = :counter")->setParameter('counter', count($category->getProducts()))
            ->getQuery()
            ->getResult();
        return $query_obj;
    }

    /**
     * getUnicCharacteristicsByCategory
     *
     * We must select only those Characteristics that defined in
     * current Category.
     * Do not use "Categories<=>Products" relationship.
     *
     * @param \AppBundle\Entity\Categories $category
     *
     * @return mixed
     */
    public function getUnicCharacteristicsByCategory(
        \AppBundle\Entity\Categories $category
    )
    {
        $query_obj = $this->createQueryBuilder('characteristics')
            ->select('characteristics')
            ->innerJoin('characteristics.categories', 'categories')->addSelect('categories')
            ->where("categories.id = :category")->setParameter('category', $category->getId())
            ->getQuery()
            ->getResult();
        return $query_obj;
    }

}
