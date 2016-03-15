<?php

namespace AppBundle\Entity\Repository;

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
    public function getAllCharacteristicsByCategory($category, $inFilter = null)
    {
        $query_obj = $this->createQueryBuilder('characteristics1')->select('characteristics1')
            ->innerJoin('characteristics1.characteristicValues', 'characteristicValues')->addselect('characteristicValues')
            ->innerJoin('characteristicValues.products', 'products')->addselect('products')
            ->innerJoin('products.characteristicValues', 'productCharacteristicValues')->addselect('productCharacteristicValues')
            ->innerJoin('productCharacteristicValues.categories', 'categories')->addselect('categories')
            ->innerJoin('products.baseCategory', 'baseCategory')->addselect('baseCategory')
            ->innerJoin('categories.filters', 'filters')
            ->innerJoin('filters.characteristics', 'characteristics2', 'WITH', 'characteristics1.id = characteristics2.id');
        // construct WHERE conditions
        if (isset($inFilter)) {
            $query_obj->where("characteristics1.inFilter = :inFilter")
                ->setParameter('inFilter', $inFilter);
        }

        $query_obj = $this->_em->getRepository('AppBundle:Categories')->addCategoryFilterCondition($query_obj, $category);

        $query_obj->addOrderBy('characteristics1.name', 'ASC')
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
            ->innerJoin('products.characteristicValues', 'productCharacteristicValues')->addselect('productCharacteristicValues')
            ->innerJoin('productCharacteristicValues.categories', 'categories')->addselect('categories')
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
