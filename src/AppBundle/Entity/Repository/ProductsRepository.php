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
            ->leftJoin('productModels.images', 'images')->addselect('images')
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
            ->leftJoin('productModels.images', 'images')->addselect('images')
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
     * - images;
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
                case 'images':
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
     * @param string $productAlias
     * @param string $modelAlias
     * @param string $sizeAlias
     * @return mixed
     */
    public function addSort($query, $sort, $productAlias = 'products', $modelAlias = 'productModels', $sizeAlias = 'sizes')
    {
        $query->addOrderBy("$modelAlias.inStock", 'DESC');
        switch ($sort) {
            case false:
            case 'new':
                $query->addOrderBy("$modelAlias.createTime", 'DESC');
                break;
            case 'az':
                $query->addOrderBy("$productAlias.name", 'ASC');
                break;
            case 'za':
                $query->addOrderBy("$productAlias.name", 'DESC');
                break;
            case 'cheap':
                // todo replace with sql COALESCE
                $query->addOrderBy("$sizeAlias.price", 'ASC');
                break;
            case 'expensive':
                $query->addOrderBy("$sizeAlias.price", 'DESC');
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

        $query->addOrderBy("$modelAlias.createTime", 'DESC');
        $query->addOrderBy("$modelAlias.id", 'DESC');

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
            ->leftJoin('prodMod.images', 'prodMImg')->addselect('prodMImg')
            ->where('prod.active = 1 AND prodMod.published = 1 AND prodMod.alias = :alias')
            ->setParameter('alias', $modelAlias);
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
            ->leftJoin('prodMod.images', 'prodImg')->addselect('prodImg')
            ->where('prodChName.inFilter = 1 AND prod.active = 1 AND prodMod.published = 1 AND prodMod.id = :id')
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
            ->leftJoin('prodMod.images', 'prodImg')->addselect('prodImg')
            ->where('prod.active = 1 AND prodMod.published = 1 AND prodMod.alias = :alias')
            ->setParameter('alias', $productModelAlias);
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
    ) {
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
     * @param $ids
     * @return array
     */
    public function getFilteredProductsToCategoryQuery($category, $characteristicValues, $filters, $ids = array())
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
            ->leftJoin('productModels.images', 'images')->addselect('images')
            ->innerJoin('productModels.sizes', 'sizes')->addselect('sizes')
            ->innerJoin('sizes.size', 'modelSize')->addselect('modelSize')
            ->andWhere('productModels.published = 1 AND baseCategory.active = 1')
            ->innerJoin('characteristicValues.characteristics', 'characteristics');

        if(!empty($ids)){
            $builder->andWhere("products.id IN(:productsIds)")
                ->setParameter('productsIds', array_values($ids));
        }

        $builder = $this->_em->getRepository('AppBundle:Categories')->addCategoryFilterCondition($builder, $category);

        $builder = $this->addCharacteristicsCondition($builder, $characteristicValues);

        $builder = $this->addFiltersToQuery($builder, $filters);

        $builder = $this->addActiveConditionsToQuery($builder);

        $builder = $this->_em->getRepository('AppBundle:ProductModelSpecificSize')
            ->addPriceToQuery($builder, $filters);

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
    public function addCharacteristicsCondition(
        $builder,
        $characteristicValues,
        $productsAlias = 'productModels',
        $characteristicValuesAlias = 'characteristicValues',
        $characteristicsAlias = 'characteristics'
    ) {
        if ($characteristicValues) {
            $builder->andWhere($builder->expr()->in("$characteristicValuesAlias.id", $characteristicValues))
                ->groupBy("$productsAlias.id")
                ->having('COUNT(DISTINCT ' . $characteristicsAlias . '.id) >=
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
    public function addFiltersToQuery($builder, $filters, $alias = 'productModels')
    {
        if ($colors = Arr::get($filters, 'colors')) {
            $colors = explode(',', $colors);
            $builder->andWhere($builder->expr()->in("$alias.productColors", $colors));
        }

        return $builder;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $builder
     * @param string $modelAlias
     * @param string $productAlias
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function addActiveConditionsToQuery($builder, $modelAlias = 'productModels', $productAlias = 'products')
    {
        $builder->andWhere("$modelAlias.published = 1 AND $productAlias.active = 1");

        return $builder;
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
