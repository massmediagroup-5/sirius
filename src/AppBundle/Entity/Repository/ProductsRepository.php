<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Illuminate\Support\Arr;

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
    private $query_obj;

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
        $this->query_obj = $this->createQueryBuilder('products')
            ->select('products');
        // Add a starting Joins.
        $this->query_obj
            ->innerJoin('products.characteristicValues', 'characteristicValues')->addselect('characteristicValues')
            ->innerJoin('characteristicValues.categories', 'categories')->addselect('categories')
            ->innerJoin('products.actionLabels', 'actionLabels')->addselect('actionLabels')
            ->innerJoin('products.productModels', 'productModels')->addselect('productModels')
            ->innerJoin('productModels.productColors', 'productColors')->addselect('productColors')
            ->innerJoin('productModels.skuProducts', 'skuProducts')->addselect('skuProducts')
            ->leftJoin('productModels.productModelImages', 'productModelImages')->addselect('productModelImages')
            ->innerJoin('products.baseCategory', 'baseCategory')->addselect('baseCategories')
            ->leftJoin('productModels.sizes', 'sizes')->addselect('sizes')
            ->orderBy('productModels.priority', 'ASC');
    }

    /**
     * start
     *
     * @return this
     */
    public function start()
    {
        $this->query_obj = $this->createQueryBuilder('products')
            ->select('products');
        // Add a starting Joins.
        $this->query_obj
            ->innerJoin('products.characteristicValues', 'characteristicValues')->addselect('characteristicValues')
            ->innerJoin('characteristicValues.categories', 'categories')->addselect('categories')
            ->innerJoin('products.actionLabels', 'actionLabels')->addselect('actionLabels')
            ->innerJoin('products.productModels', 'productModels')->addselect('productModels')
            ->innerJoin('productModels.productColors', 'productColors')->addselect('productColors')
            ->innerJoin('productModels.skuProducts', 'skuProducts')->addselect('skuProducts')
            ->leftJoin('productModels.productModelImages', 'productModelImages')->addselect('productModelImages')
            ->innerJoin('products.baseCategory', 'baseCategories')->addselect('baseCategories')
            ->orderBy('productModels.priority', 'ASC');
        return $this;
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
     * @param Doctrine\ORM\Query $dql DQL Query Object
     * @param integer $page Current page (defaults to 1)
     * @param integer $limit The total number per page (defaults to 5)
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function paginate($dql, $page = 1, $limit = 9)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))// Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
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
     * - productsBaseCategories;
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
     * @return mixed
     */
    public function addWhere(array $where = array())
    {
        // construct WHERE conditions
        foreach ($where as $whereKey => $whereValue) {
            switch ($whereKey) {
                case 'categories':
                case 'baseCategories':
                case 'products':
                case 'productModels':
                case 'productColors':
                case 'productsBaseCategories':
                case 'productModelImages':
                case 'actionLabels':
                    $table = $whereKey;
                    break;
            }
            foreach ($whereValue as $columnKey => $columnValue) {
                if ($columnKey == 'price_from') {
                    $this->query_obj
                        ->andWhere("{$table}.price >= :{$columnKey}")
                        ->setParameter($columnKey, $columnValue);
                } elseif ($columnKey == 'price_to') {
                    $this->query_obj
                        ->andWhere("{$table}.price <= :{$columnKey}")
                        ->setParameter($columnKey, $columnValue);
                } else {
                    $this->query_obj
                        ->andWhere("{$table}.{$columnKey} = :{$columnKey}")
                        ->setParameter($columnKey, $columnValue);
                }
            }
        }
        return $this;
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
        $this->query_obj
            ->innerJoin('products.characteristicValues', 'characteristicValues')->addSelect('characteristicValues')
            ->innerJoin('characteristicValues.characteristics', 'characteristics')->addSelect('characteristics')
            ->andWhere("characteristicValues.id IN (:characteristicValues)")
            ->setParameter('characteristicValues', $characteristicValues);
        return $this;
    }

    /**
     * @param $query
     * @param $sort
     * @param string $alias
     * @return mixed
     */
    public function addSort($query, $sort, $alias = 'productModels')
    {
        $query->orderBy("$alias.inStock", 'DESC');
        switch ($sort) {
            case false:
            case 'az':
                $query->addOrderBy("$alias.name", 'ASC');
                break;
            case 'za':
                $query->addOrderBy("$alias.name", 'DESC');
                break;
            case 'cheap':
                $query->addOrderBy("$alias.price", 'ASC');
                break;
            case 'expensive':
                $query->addOrderBy("$alias.price", 'DESC');
                break;
            case 'novelty':
                //$query->orderBy('prodSkuVnd.priority', 'ASC');
                break;
            case 'action':
                //$query->orderBy('prodSkuVnd.priority', 'ASC');
                break;
            default:
                break;
        }

        return $query;
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
    public function addCountCharacteristics($chValue)
    {
        $this->query_obj
            ->addSelect('COUNT(DISTINCT characteristics.id) as chcount')
            ->groupBy('products.id')
            //->having('chcount >= :count')->setParameter('count', $count)

            ->having('chcount >=
                (
                    SELECT COUNT( DISTINCT incchar.id )
                    FROM \AppBundle\Entity\Characteristics as incchar
                    JOIN incchar.characteristicValues as inccharval
                    WHERE inccharval.id IN (' . implode(',', $chValue) . ')
                    OR inccharval.id = characteristicValues.id
                )
            ');
        return $this;
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
        $query = $this->query_obj->getQuery();
        //dump($query->getSql());
        $result = $this->paginate($query, $currentPage);

        return $result;
    }

    /**
     * Return query object
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQuery()
    {
        return $this->query_obj->getQuery();
    }

    /**
     * Get product and category info by their aliases.
     *
     * @param mixed $modelAlias
     * @return mixed
     */
    public function getProductInfoByAlias($modelAlias)
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
            ->setParameter('alias', $modelAlias)
            ->orderBy('prodSkuVnd.priority', 'ASC');
        return $query_obj->getQuery()->getSingleResult();
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
            ->orderBy('prodChVal.characteristics', 'ASC');
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
            ->orderBy('prodSkuVnd.priority', 'ASC');
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
        $this->query_obj = $this->createQueryBuilder('products')
            ->select('products')
            ->innerJoin('products.characteristicValues', 'characteristicValues')->addselect('characteristicValues')
            ->innerJoin('characteristicValues.categories', 'categories')->addselect('categories')
            ->where("categories.id = :category")->setParameter('category', $category->getId());
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
        $this->query_obj
            ->andWhere('characteristicValues.id IN (:values' . $this->counter . ')')
            ->setParameter('values' . $this->counter, $array);
        ++$this->counter;
        return $this;
    }

    /**
     * @param $category
     * @param $characteristicValues
     * @param $filters
     * @return array
     */
    public function getFilteredProductsToCategoryQuery($category, $characteristicValues, $filters)
    {
        $builder = $this->createQueryBuilder('products')
            ->select('products');
        // Add a starting Joins.
        $builder
            ->innerJoin('products.baseCategory', 'baseCategory')->addselect('baseCategory')
            ->innerJoin('products.characteristicValues', 'characteristicValues')->addSelect('characteristicValues')
            ->innerJoin('characteristicValues.categories', 'categories')
            ->innerJoin('products.productModels', 'productModels')->addselect('productModels')
            ->innerJoin('productModels.productColors', 'productColors')->addselect('productColors')
            ->innerJoin('productModels.skuProducts', 'skuProducts')->addselect('skuProducts')
            ->leftJoin('productModels.productModelImages', 'productModelImages')->addselect('productModelImages')
            // todo fix this hell, doctrine lazy load is slower then over 100 queries
//            ->leftJoin('productModels.sizes', 'sizes')->addselect('sizes')
            ->andWhere('productModels.published = 1 AND productModels.active = 1 AND baseCategory.active = 1')
            ->innerJoin('characteristicValues.characteristics', 'characteristics');

        $builder = $this->_em->getRepository('AppBundle:Categories')->addCategoryFilterCondition($builder, $category);

        $builder = $this->addCharacteristicsCondition($builder, $characteristicValues);

        $builder = $this->addPriceToQuery($builder, $filters);

        $builder = $this->addSort($builder, Arr::get($filters, 'sort'));

        return $builder->getQuery();
    }

    /**
     * @param $category
     * @param $characteristicValues
     * @param $filters
     * @return array
     */
    public function getFilteredProductsToCategoryCount($category, $characteristicValues, $filters)
    {
        return $this->getFilteredProductsToCategoryQuery($category, $characteristicValues, $filters)
            ->select('COUNT(products)')
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $builder
     * @param $characteristicValues
     * @param string $productsAlias
     * @param string $characteristicValuesAlias
     * @param string $characteristicsAlias
     * @return mixed
     */
    public function addCharacteristicsCondition($builder, $characteristicValues, $productsAlias = 'products', $characteristicValuesAlias = 'characteristicValues', $characteristicsAlias = 'characteristics')
    {
        if ($characteristicValues) {
            $builder->andWhere($builder->expr()->in("$characteristicValuesAlias.id", $characteristicValues))
                ->groupBy("$productsAlias.id")
                ->having('COUNT(DISTINCT characteristics.id) >=
                (
                    SELECT COUNT( DISTINCT incchar.id )
                    FROM \AppBundle\Entity\Characteristics as incchar
                    JOIN incchar.characteristicValues as inccharval
                    WHERE inccharval.id IN (' . implode(',', $characteristicValues) . ')
                    OR inccharval.id = characteristicValues.id
                )
            ');
        }
        return $builder;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $builder
     * @param $filters
     * @param string $alias
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function addPriceToQuery($builder, $filters, $alias = 'productModels')
    {
        return $builder->andWhere($builder->expr()->gte("$alias.price", $filters['price_from']))
            ->andWhere($builder->expr()->lte("$alias.price", $filters['price_to']));
    }

    /**
     * @return array
     */
    public function getAllProductsForCategory()
    {
        //$reflectionMethod = new ReflectionMethod($this, 'sayHelloTo');
        $result = $this->query_obj
            //->expr()
            //->andX($array)
            ->getQuery()->getResult();

        return $result;
    }

}
