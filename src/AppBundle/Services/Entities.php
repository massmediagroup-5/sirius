<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Illuminate\Support\Arr;
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
     * @var array
     */
    private $newRecentlyViewed = [];

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
     * @param $categoryAlias
     * @param array $filters
     * @param int $perPage
     * @param int $currentPage
     * @param string $entity
     * @param array $ids
     * @return array|bool
     */
    public function getCollectionsByCategoriesAlias(
        $categoryAlias,
        $filters = [],
        $perPage = 9,
        $currentPage = 1,
        $entity = 'Products',
        $ids = []
    ) {
        $filters['wholesaler'] = $this->container->get('security.context')->isGranted('ROLE_WHOLESALER');

        if ($sizes = Arr::get($filters, 'sizes')) {
            $filters['sizes'] = explode(',', $sizes);
        }

        $filtersForFiltersQueries = Arr::only($filters, ['share', 'wholesaler']);

        $category = $this->em
            ->getRepository('AppBundle:Categories')
            ->getCategoryInfo($categoryAlias);

        // If we didn't find any active Category.
        if (empty($category)) {
            return false;
        }

        $characteristicsValuesIds = [];
        foreach ((array)$filters as $key => $filter) {
            // numeric keys - is characteristics_ids
            if (is_numeric($key)) {
                $characteristicsValuesIds = array_merge($characteristicsValuesIds, explode(',', $filter));
            }
        }
        $price_filter = $this->em->getRepository('AppBundle:ProductModels')
            ->getPricesIntervalForFilters($category, [], $filtersForFiltersQueries);

        if (empty($filters['price_from']) || $filters['price_from'] < $price_filter['min_price']) {
            $filters['price_from'] = $price_filter['min_price'];
        }
        if (empty($filters['price_to']) || $filters['price_to'] > $price_filter['max_price']) {
            $filters['price_to'] = $price_filter['max_price'];
        }

        $products = $this->em->getRepository("AppBundle:$entity")
            ->getFilteredProductsToCategoryQuery($category, $characteristicsValuesIds, $filters, $ids);

        $colors = $this->em->getRepository('AppBundle:ProductColors')
            ->getColorsForFilteredProducts($category, [], $filtersForFiltersQueries);

        $sizes = $this->em->getRepository('AppBundle:ProductModelSizes')
            ->findForFilter($category, $characteristicsValuesIds, $filters);

        $products = $this->container->get('knp_paginator')->paginate(
            $products,
            $currentPage,
            $perPage,
            ['wrap-queries' => true]
        );

        $characteristics = [];

        $characteristicValuesResult = $this->em->getRepository("AppBundle:CharacteristicValues")
            ->getWithModelsCount($category, $characteristicsValuesIds, $filters);

        foreach ($characteristicValuesResult as $value) {
            $characteristic = $value[0]->getCharacteristics();
            if (!isset($characteristics[$characteristic->getId()])) {
                $characteristics[$characteristic->getId()]['characteristic'] = $characteristic;
            }
            $characteristics[$characteristic->getId()]['values'][] = $value;
        }

        if ($share = Arr::get($filters, 'share')) {
            $share = $this->em->getRepository('AppBundle:Share')->find($share);
        }


        return compact('category', 'characteristicValues', 'products', 'characteristics', 'price_filter', 'colors',
            'filters', 'share', 'sizes');
    }

    /**
     * @param $modelId
     * @return mixed
     */
    public function getModelsByProduct($modelId)
    {
        return $this->em
            ->getRepository('AppBundle:ProductModels')
            ->getModelsByProductId($modelId);

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
            ->getModelsByProductId($result['product']['id'], $productModelAlias);
        if (empty($result)) {
            return false;
        }
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
        $base_category_id = array_column($base_category, 'id');
        $product_id = array_column($product, 'id');
        $result = array();
        foreach ($product_id as $value) {
            if (in_array($value, $base_category_id)) {
                $result[] = $value;
            }
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
        if (!$recently_viewed) {
            $recently_viewed = array();
        }
        if (!isset($recently_viewed[$productModelsId])) {
            $recently_viewed[$productModelsId] = $productModelsId;
            $this->newRecentlyViewed[$productModelsId] = $productModelsId;
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
        $recentlyViewed = $this->container->get('session')->get('recently_viewed');
        if ($recentlyViewed) {
            // Skip item when it added by current object (not from session)
            $newViewedCount = count($this->newRecentlyViewed);
            // When last ids from session equals to new
            if (array_slice($recentlyViewed, -$newViewedCount, $newViewedCount, true) == $this->newRecentlyViewed) {
                $recentlyViewed = array_slice($recentlyViewed, 0, count($recentlyViewed) - $newViewedCount);
            }

            return $this->em->getRepository('AppBundle:ProductModels')->getActiveModelsByIds($recentlyViewed);
        }
        return [];
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
        array_walk($entities, function ($value, $key) use (&$result, $column) {
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
            ->setParameter('startTime', $now->format('Y-m-d'))
            ->setParameter('endTime', $now->format('Y-m-d'));
        try {
            return $promotion->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /**
     * getFilteredProducts
     *
     * @param mixed $productWhere
     * @param mixed $filterValues
     * @param mixed $currentPage
     * @param mixed $filters
     * @param mixed $sort
     *
     * @return mixed
     */
    private function getFilteredProducts($productWhere, $filterValues, $currentPage, $filters, $sort)
    {
        $result = $this->em
            ->getRepository('AppBundle:Products')->start();
        if (!empty($filterValues)) {
            $characteristicValues = array_values($filterValues);
            $result = $result
                ->addCountCharacteristics($characteristicValues)
                ->joinFilters($characteristicValues);
        }
        $result = $result
            ->addWhere($productWhere)
            ->addSort($sort)
            ->getAllProducts($productWhere, $currentPage);

        return $result;
    }

}
