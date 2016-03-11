<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ProductsRepository
 *
 */
class ProductsRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * query_obj
     *
     * @var mixed
     */
    private $queryObj;

    /**
     * queryCountObj
     *
     * @var mixed
     */
    private $queryCountObj;

    /**
     * counter
     *
     * @var mixed
     */
    private $counter;

    /**
     * __construct
     *
     * @param mixed $em
     * @param \Doctrine\ORM\Mapping\ClassMetadata $class
     */
    public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->queryObj = $this->createQueryBuilder('products')
            ->select('products');
        // Add a starting Joins.
        $this->queryObj
            ->innerJoin('products.categories', 'categories')->addselect('categories')
            ->innerJoin('products.actionLabels', 'actionLabels')->addselect('actionLabels')
            ->innerJoin('products.productModels', 'productModels')->addselect('productModels')
            ->innerJoin('productModels.productColors', 'productColors')->addselect('productColors')
            ->innerJoin('productModels.skuProducts', 'skuProducts')->addselect('skuProducts')
            ->leftJoin('productModels.productModelImages', 'productModelImages')->addselect('productModelImages')
            ->innerJoin('products.baseCategory', 'baseCategory')->addselect('baseCategory')
            ->orderBy('productModels.priority', 'ASC')
            ;
    }

    /**
     * Paginator Helper
     *
     * Pass through a query object, current page & limit
     * the offset is calculated from the page and limit
     * returns an `Paginator` instance, which you can call the following on:
     *
     *     $paginator->getIterator()->count() # Total fetched (ie: `5` posts)
     *     $paginator->count() # Count of ALL posts (ie: `20` posts)
     *     $paginator->getIterator() # ArrayIterator
     *
     * @param Doctrine\ORM\Query $dql   DQL Query Object
     * @param integer            $page  Current page (defaults to 1)
     * @param integer            $limit The total number per page (defaults to 5)
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function paginate($dql, $page = 1, $limit = 9)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }

    /**
     * addWhere
     *
     * @param array $where
     * @param $forCount
     *
     * @return mixed
     */
    public function addWhere(array $where = array(), $forCount = false)
    {
        $object = $forCount ? @$this->queryCountObj : $this->queryObj;

        return $this->makeWhere($where, $object);
    }

    /**
     * joinFilters
     *
     * Add JOIN with characteristicValues
     *
     * @param array $characteristicValues
     *
     * @return mixed
     */
    public function joinFilters(array $characteristicValues = array())
    {
        $this->queryObj
            ->innerJoin('products.characteristicValues', 'characteristicValues')->addSelect('characteristicValues')
            ->innerJoin('characteristicValues.characteristics', 'characteristics')->addSelect('characteristics')
            ->andWhere("characteristicValues.id IN (:characteristicValues)")
            ->setParameter('characteristicValues', $characteristicValues)
            ;
        return $this;
    }

    /**
     * addSort
     *
     * Add sorting
     *
     * @param mixed $sort
     *
     * @return mixed
     */
    public function addSort($sort)
    {
        $this->queryObj->orderBy('productModels.inStock', 'DESC');
        switch ($sort) {
            case 'az':
                $this->queryObj->addOrderBy('productModels.name', 'ASC');
                break;
            case 'za':
                $this->queryObj->addOrderBy('productModels.name', 'DESC');
                break;
            case 'cheap':
                $this->queryObj->addOrderBy('productModels.price', 'ASC');
                break;
            case 'expensive':
                $this->queryObj->addOrderBy('productModels.price', 'DESC');
                break;
            case 'novelty':
                //$this->queryObj->orderBy('prodSkuVnd.priority', 'ASC');
                break;
            case 'action':
                //$this->queryObj->orderBy('prodSkuVnd.priority', 'ASC');
                break;
            default:
                break;
        }

        return $this;
    }

    /**
     * addCountCharacteristics
     *
     * All magic here!
     *
     * @param mixed $count
     *
     * @return mixed
     */
    public function addCountCharacteristics($count)
    {
        $this->queryObj
            ->addSelect($this->queryObj->expr()->count('DISTINCT characteristics.id') . ' as chcount')
            ->groupBy('products.id')
            ->having('chcount >= :count')->setParameter('count', $count)
            ;
        return $this;
    }

    /**
     * getCountQuery
     *
     */
    public function getCountQuery()
    {
        return $this->queryCountObj;
    }

    /**
     * getAllProducts
     *
     * Get all Products in the category with ProductModels.
     *
     * @param array $where
     * @param integer $currentPage
     *
     * @return mixed
     *
     */
    public function getAllProducts(array $where = array(), $currentPage = 1)
    {
        $query = $this->queryObj->getQuery();
        $result = $this->paginate($query, $currentPage);

        return $result;
    }

    /**
     * getProductInfoByAlias
     *
     * Get product and category info by their aliases.
     *
     * @param mixed $productModelAlias
     *
     * @return mixed
     */
    public function getProductInfoByAlias($productModelAlias)
    {
        $query_obj = $this->createQueryBuilder('prod')
            ->select('prod')
            ->innerJoin('prod.actionLabels', 'act')->addselect('act')
            ->innerJoin('prod.productModels', 'prodMod')->addselect('prodMod')
            ->innerJoin('prod.characteristicValues', 'prodChVal')->addselect('prodChVal')
            ->innerJoin('prod.baseCategory', 'catb')->addselect('catb')
            //->innerJoin('catb.characteristicValues', 'catbChVal')->addselect('catbChVal')
            ->innerJoin('prodChVal.characteristics', 'prodChName')->addselect('prodChName')
            ->innerJoin('prodMod.productColors', 'prodCol')->addselect('prodCol')
            ->innerJoin('prodMod.skuProducts', 'prodSku')->addselect('prodSku')
            ->innerJoin('prodSku.vendors', 'prodSkuVnd')->addselect('prodSkuVnd')
            ->leftJoin('prodMod.productModelImages', 'prodMImg')->addselect('prodMImg')
            ->where('prod.active = 1 AND prod.published = 1 AND prodMod.active = 1 AND prodMod.published = 1 AND prodMod.alias = :alias')
            ->setParameter('alias', $productModelAlias)
            ->orderBy('prodSkuVnd.priority', 'ASC')
        ;
        return $query_obj->getQuery()->getSingleResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * getProductInfoById
     *
     * Get product and category info by their aliases.
     *
     * @param mixed $productModelId
     *
     * @return mixed
     */
    public function getProductInfoById($productModelId)
    {
        $query_obj = $this->createQueryBuilder('prod')
            ->select('prod')
            ->innerJoin('prod.actionLabels', 'act')->addselect('act')
            ->innerJoin('prod.productModels', 'prodMod')->addselect('prodMod')
            ->innerJoin('prod.characteristicValues', 'prodChVal')->addselect('prodChVal')
            ->innerJoin('prodChVal.characteristics', 'prodChName')->addselect('prodChName')
            ->innerJoin('prodMod.skuProducts', 'prodSku')->addselect('prodSku')
            ->leftJoin('prodMod.productModelImages', 'prodMImg')->addselect('prodMImg')
            ->where('prodChName.inFilter = 1 AND prod.active = 1 AND prod.published = 1 AND prodMod.active = 1 AND prodMod.published = 1 AND prodMod.id = :id')
            ->setParameter('id', $productModelId)
            ->orderBy('prodChVal.characteristics', 'ASC')
        ;
        return $query_obj->getQuery()->getSingleResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * getProductInfoByAliasForSearch
     *
     * Get product and category info by their aliases.
     *
     * @param mixed $productModelAlias
     *
     * @return mixed
     */
    public function getProductInfoByAliasForSearch($productModelAlias)
    {
        $query_obj = $this->createQueryBuilder('prod')
            ->select('prod')
            ->innerJoin('prod.actionLabels', 'act')->addselect('act')
            ->innerJoin('prod.productModels', 'prodMod')->addselect('prodMod')
            //->innerJoin('prod.characteristicValues', 'prodChVal')->addselect('prodChVal')
            ->innerJoin('prod.baseCategory', 'catb')->addselect('catb')
            //->innerJoin('catb.characteristicValues', 'catbChVal')->addselect('catbChVal')
            //->innerJoin('prodChVal.characteristics', 'prodChName')->addselect('prodChName')
            ->innerJoin('prodMod.productColors', 'prodCol')->addselect('prodCol')
            ->innerJoin('prodMod.skuProducts', 'prodSku')->addselect('prodSku')
            ->innerJoin('prodSku.vendors', 'prodSkuVnd')->addselect('prodSkuVnd')
            ->leftJoin('prodMod.productModelImages', 'prodMImg')->addselect('prodMImg')
            ->where('prod.active = 1 AND prod.published = 1 AND prodMod.active = 1 AND prodMod.published = 1 AND prodMod.alias = :alias')
            ->setParameter('alias', $productModelAlias)
            ->orderBy('prodSkuVnd.priority', 'ASC')
        ;
        return $query_obj->getQuery()->getSingleResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * setAllProductsForCategory
     *
     * We must select all Products with those CharacteristicValues what belong
     * Characteristics defined in current Category.
     * Do not use "Categories<=>CharacteristicValues" relationship.
     *
     * @param \AppBundle\Entity\Categories $category
     *
     * @return this
     */
    public function setAllProductsForCategory(
        \AppBundle\Entity\Categories $category
    )
    {
        $this->queryObj = $this->createQueryBuilder('products')
            ->select('products')
            ->innerJoin('products.characteristicValues', 'characteristicValues')->addselect('characteristicValues')
            ->innerJoin('characteristicValues.categories', 'categories')->addselect('categories')
            ->where("categories.id = :category")->setParameter('category', $category->getId())
            ;
        $this->counter = 1;
        return $this;
    }

    /**
     * addWhereIn
     *
     * @param mixed $array
     *
     * @return this
     */
    public function addWhereIn($array)
    {
        $this->queryObj
            ->andWhere('characteristicValues.id IN (:values'.$this->counter.')')
            ->setParameter('values'.$this->counter, $array)
            ;
        ++$this->counter;
        return $this;
    }

    /**
     * getAllProductsForCategory
     *
     * @return this
     */
    public function getAllProductsForCategory()
    {
        $result = $this->queryObj
            ->getQuery()->getResult();

        return $result;
    }

    /**
     * startCount
     *
     * @return this
     */
    public function startCount()
    {
        $this->queryCountObj = $this->createQueryBuilder('productsCount')
            ->select('productsCount.id')
            ;
        // Add a starting Joins.
        $this->queryCountObj
            ->innerJoin('productsCount.categories', 'categoriesCount')
            ->innerJoin('productsCount.actionLabels', 'actionLabels')
            ->innerJoin('productsCount.productModels', 'productModelsCount')
            ->innerJoin('productModelsCount.productColors', 'productColors')
            ->innerJoin('productModelsCount.skuProducts', 'skuProducts')
            ->innerJoin('productsCount.baseCategory', 'baseCategories')
            ;
        return $this;
    }

    /**
     * addCountCharacteristicsForCount
     *
     * ... and here, too :(
     *
     * @param mixed $count
     *
     * @return mixed
     */
    public function addCountCharacteristicsForCount($count, $chValue)
    {
        $this->queryCountObj
            //->addSelect($this->queryCountObj->expr()->count('DISTINCT characteristics.id') . ' as chcount')
            //->addSelect($this->queryCountObj->expr()->count('DISTINCT characteristicsCount.id') . ' as charcount')
            //->select('productsCount.id as prodId')
            ->groupBy('productsCount.id')
            ->having('
                COUNT( DISTINCT characteristicsCount.id ) >=
                (
                    SELECT COUNT( DISTINCT incchar.id )
                    FROM \AppBundle\Entity\Characteristics as incchar
                    JOIN incchar.characteristicValues as inccharval
                    WHERE inccharval.id IN (' . implode(',', $chValue) . ')
                    OR inccharval.id = characteristicValues.id
                    
                )
            ')
            //->setParameter('count', $count)
            ;
        return $this;
    }

    /**
     * joinFiltersForCount
     *
     * Add JOIN with characteristicValues for Count products for every
     * CharacteristicValue in filter.
     *
     * @param array $characteristicValues
     *
     * @return mixed
     */
    public function joinFiltersForCount(array $characteristicValues = array())
    {
        $this->queryCountObj
            ->innerJoin('productsCount.characteristicValues', 'characteristicValuesCount')
            ->innerJoin('characteristicValuesCount.characteristics', 'characteristicsCount')
            ->andWhere("characteristicValuesCount.id IN (:characteristicValuesCount)")
            ->setParameter('characteristicValuesCount', $characteristicValues)
            ;
        return $this;
    }

    /**
     * makeWhere
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
     * @param mixed $obj
     *
     * @return mixed
     */
    private function makeWhere(array $where = array(), $obj)
    {
        // construct WHERE conditions
        foreach ($where as $table => $whereValue) {
            foreach($whereValue as $columnKey=> $columnValue) {
                $unicParametr = $table . $columnKey;
                switch ($columnKey) {
                    case 'price_from':
                        $obj->andWhere("{$table}.price >= :{$unicParametr}");
                        break;
                    case 'price_to':
                        $obj->andWhere("{$table}.price <= :{$unicParametr}");
                        break;
                    default:
                        $obj->andWhere("{$table}.{$columnKey} = :{$unicParametr}");
                        break;
                }
                $obj->setParameter($unicParametr, $columnValue);
            }
        }

        return $this;
    }

}
