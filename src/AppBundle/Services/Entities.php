<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity as Entity;

/**
 * Class: Entities
 * @author linux0uid
 */
class Entities
{

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
     * __construct
     *
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * getAllActiveCategoriesForMenu
     *
     * @return mixed
     */
    public function getAllActiveCategoriesForMenu()
    {
        $category_list = $this->em
            ->getRepository('AppBundle:Categories')
            ->getAllActiveCategoriesForMenu();
        return $category_list;
    }
    /**
     * getCollectionsByCategoriesAlias
     *
     * Return array with two collections for Characteristics and Products
     *
     * @param mixed $categoryAlias
     * @param mixed $filters
     * @param integer $currentPage
     *
     * @return array
     */
    public function getCollectionsByCategoriesAlias($categoryAlias, $filters = null, $currentPage = 1, $sort = 'az')
    {
        $category = array(
            'alias' => $categoryAlias,
            'active' => 1,
        );

        $result['price_filter'] = $this->em
            ->getRepository('AppBundle:ProductModels')
            ->createQueryBuilder('productModels')
            ->select('productModels ,MIN(productModels.price) AS min_price, MAX(productModels.price) AS max_price')
            ->where('productModels.active = 1 AND productModels.published = 1')
            ->andWhere('categories.alias = :alias')
            ->innerJoin('productModels.products', 'products')->addselect('products')
            ->innerJoin('products.categories', 'categories')->addselect('categories')
            ->setParameter('alias',$categoryAlias)
            ->getQuery()->getSingleResult();

        $price_from = $filters['price_from'] ? $filters['price_from'] : $result['price_filter']['min_price'];
        $price_to = $filters['price_to'] ? $filters['price_to'] : $result['price_filter']['max_price'];
        
        $productWhere = array(
            'categories'  => $category,
            'products'    => array(
                'active'      => 1,
                'published'   => 1,
            ),
            'productModels' => array(
                'active'      => 1,
                'published'   => 1,
                'price_from'  => $price_from,
                'price_to'    => $price_to
            ),
        );
        $productWhereForCharacteristics = array(
            'categoriesCount'  => $category,
            'productsCount'    => array(
                'active'      => 1,
                'published'   => 1,
            ),
            'productModelsCount' => array(
                'active'      => 1,
                'published'   => 1,
                'price_from'  => $price_from,
                'price_to'    => $price_to
            ),
        );
        $result['category'] = $this->em
            ->getRepository('AppBundle:Categories')
            ->getCategoryInfo($categoryAlias);

        // If we didn't find any active Category.
        if(empty($result['category']))
            return false;

        // Added joins and conditions for our Filters.
        unset($filters['page'],$filters['sort'],$filters['price_from'],$filters['price_to']);
        $filterValues = [];
        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $filter = explode(',', $filter);
                $filterValues = array_merge($filterValues, $filter);
            }
        }
        /*
         * If we have active some filters, we must to create query like this:
         *
         *    SELECT 
         *    	COUNT(DISTINCT characteristics_.id) AS chcount, 
         *    	...select here...
         *    FROM 
         *    	products p1_ 
         *    	...joins here...
         *    WHERE 
         *    	characteristics_.id IN (?) 
         *    	...where here...
         *    GROUP BY 
         *    	p1_.id 
         *    HAVING 
         *    	chcount >= {count_of_active_filters} 
         */
        // Make Products
        $result['products'] = $this->em
            ->getRepository('AppBundle:Products');
        // Make Characteristics
        $productsQueryForCharacteristicsCount = $this->em
            ->getRepository('AppBundle:Products')
            ->startCount();
        // Set filsers in query

        if(!empty($filterValues)) {
            $characteristicValues = array_values($filterValues);
            $result['products'] = $result['products']
                ->addCountCharacteristics(count($filters))
                ->joinFilters($characteristicValues);
            $productsQueryForCharacteristicsCount = $productsQueryForCharacteristicsCount
                ->addCountCharacteristicsForCount(count($filters), $characteristicValues)
                ->joinFiltersForCount($characteristicValues);
        }

        $result['products'] = $result['products']
            ->addWhere($productWhere)
            ->addSort($sort)
            ->getAllProducts($productWhere, $currentPage);
        $productsQueryForCharacteristicsCount = $productsQueryForCharacteristicsCount
            ->addWhere($productWhereForCharacteristics, $forCount = true)
            ;
        $result['characteristics'] = $this->em
            ->getRepository('AppBundle:Characteristics')
            ->getAllCharacteristicsByCategory($category, $inFilter = true, $productsQueryForCharacteristicsCount->getCountQuery());

        return $result;
    }

    /**
     * getProductInfoByAlias
     *
     * @param mixed $productModelAlias
     *
     * @return array
     *
     * Return array with Product information
     */
    public function getProductInfoByAlias($productModelAlias)
    {
        $result['product'] = $this->em
            ->getRepository('AppBundle:Products')
            ->getProductInfoByAlias($productModelAlias);
        //$base_category = $result['product']['productsBaseCategories']['categories']['characteristicValues'];
        $product = $result['product']['characteristicValues'];
        //$result['characteristics'] = $this->getMainCharacteristicList($base_category, $product);
        $result['models'] = $this->em
            ->getRepository('AppBundle:ProductModels')
            ->getModelsByProductId($result['product']['id'],$productModelAlias);
        if(empty($result))
            return false;
        return $result;
    }

    /**
     * getProductInfoByAliasForSearch
     *
     * @param mixed $productModelAlias
     *
     * @return array
     *
     * Return array with Product information
     */
    public function getProductInfoByAliasForSearch($productModelAlias)
    {
        $result['product'] = $this->em
            ->getRepository('AppBundle:Products')
            ->getProductInfoByAliasForSearch($productModelAlias);
        //dump($result);
        //$base_category = $result['product']['productsBaseCategories']['categories']['characteristicValues'];
        $product = $result['product']['characteristicValues'];
        //$result['characteristics'] = $this->getMainCharacteristicList($base_category, $product);
        $result['models'] = $this->em
            ->getRepository('AppBundle:ProductModels')
            ->getModelsByProductId($result['product']['id'],$productModelAlias);
        if(empty($result))
            return false;
        return $result;
    }

    /**
     * getMainCharacteristicList
     *
     * @param mixed $base_category
     * @param mixed $product
     *
     * @return array
     */
    public function getMainCharacteristicList($base_category, $product)
    {
        $base_category_id = array_column($base_category,'id');
        $product_id = array_column($product,'id');
        $result = array();
        foreach($product_id as $value){
            if(in_array($value, $base_category_id))$result[]= $value;
        }
        return array_values($result);
    }

    /**
     * setRecentlyViewed
     *
     * @param mixed $productModelsId
     *
     * @return array
     */
    public function setRecentlyViewed($productModelsId)
    {
        $recently_viewed = $this->container->get('session')->get('recently_viewed');
        if(!$recently_viewed) $recently_viewed = array();
        if(!isset($recently_viewed[$productModelsId])){
            $recently_viewed[$productModelsId] = $productModelsId;
        }
        $this->container->get('session')->set('recently_viewed', $recently_viewed);
    }

    /**
     * getRecentlyViewed
     *
     * @return mixed array
     */
    public function getRecentlyViewed()
    {
        $recently_viewed = $this->container->get('session')->get('recently_viewed');
        if(isset($recently_viewed)){
            foreach($recently_viewed as $key => $model){
                $recently_viewed[$key] = $this->em->getRepository('AppBundle:ProductModels')->find($model);
            }
        }
        return $recently_viewed;
    }

    /**
     * getArrayOf
     *
     * Get Array `id` or othe field from collection of Entity objects.
     *
     * @param mixed $column Name of column of Entities
     * @param array $entities
     *
     * @return array
     */
    public function getArrayOf($column = 'id', array $entities = array())
    {
        $column = 'get' . ucfirst($column);
        $result = array();
        array_walk($entities, function($value, $key) use (&$result, $column){
            $result[] = $value->$column();
        });
        return $result;
    }

    /**
     * setActionLabels
     *
     * @param Entity\Products $product
     * @param string $labelName
     *
     * @return Entity\Product
     */
    public function setActionLabels(Entity\Products $product, $labelName = 'none')
    {
        $label = $this->em->getRepository('AppBundle:ActionLabels')
            ->findOneByName($labelName);
        return $product->setActionLabels($label);
    }

    /**
     * getCurrentPromotionsForProduct
     *
     * @return mixed
     */
    public function getCurrentPromotionsForProduct()
    {
        $now = new \DateTime();
        $promotion = $this->em
            ->getRepository('AppBundle:Promotions')
            ->createQueryBuilder('promotions')
            ->where('promotions.active = 1 AND promotions.startTime <= :startTime AND promotions.endTime >= :endTime')
            ->setParameter('startTime', $now)
            ->setParameter('endTime', $now);
        try {
            return $promotion->getQuery()->getSingleResult();
        }
        catch(\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

}
