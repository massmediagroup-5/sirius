<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Arr;

/**
 * CategoriesRepository
 *
 */
class ProductModelsRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Get product and category info by their aliases.
     *
     * @param mixed $prodId
     * @param mixed $productModelAlias
     *
     * @return mixed
     */
    public function getModelsByProductId($prodId, $productModelAlias = false)
    {
        $builder = $this
            ->createQueryBuilder('productModels')
            ->innerJoin('productModels.products', 'products')->addselect('products')
            ->where('productModels.products = :prodId')
            ->setParameter('prodId', $prodId);

        $builder = $this->_em->getRepository('AppBundle:Products')->addActiveConditionsToQuery($builder);

        if ($productModelAlias) {
            $builder->andWhere('prodMod.alias != :alias')->setParameter('alias', $productModelAlias);
        }

        return $builder->getQuery()->getResult();
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function getActiveModelsByIds(array $ids)
    {
        $builder = $this
            ->createQueryBuilder('productModels')
            ->innerJoin('productModels.products', 'products')->addselect('products')
            ->where('productModels IN (:ids)')
            ->setParameter('ids', $ids);

        $builder = $this->_em->getRepository('AppBundle:Products')->addActiveConditionsToQuery($builder);

        return $builder->getQuery()->getResult();
    }

    /**
     * @param $category
     * @param $characteristicValues
     * @param $filters
     * @param bool|false $wholesale
     * @return mixed
     */
    public function getPricesIntervalForFilters($category, $characteristicValues, $filters, $wholesale = false)
    {
        $builder = $this->createQueryBuilderWithJoins();

        $builder = $this->_em->getRepository('AppBundle:Categories')->addCategoryFilterCondition($builder, $category);

        $builder = $this->_em->getRepository('AppBundle:Products')->addCharacteristicsCondition($builder,
            $characteristicValues, 'productModels');

        $builder = $this->_em->getRepository('AppBundle:Products')->addFiltersToQuery($builder, $filters);
        $builder = $this->_em->getRepository('AppBundle:Products')->addActiveConditionsToQuery($builder);

        $builder = $this->_em->getRepository('AppBundle:Products')->addSort($builder, Arr::get($filters, 'sort'));

        $builder->addSelect(
            "MAX(COALESCE(NULLIF(sizes.price, 0), NULLIF(productModels.price, 0), products.price)),
            MIN(COALESCE(NULLIF(sizes.price, 0), NULLIF(productModels.price, 0), products.price))"
        );

        $prices = $builder->getQuery()->getResult();

        return ['max_price' => Arr::get($prices, '0.1', 0), 'min_price' => Arr::get($prices, '0.2', 0)];
    }

    /**
     * @param QueryBuilder $builder
     * @param string $alias
     * @return QueryBuilder
     */
    public function addEnabledOnSiteConditions(QueryBuilder $builder, $alias = 'productModels')
    {
        return $builder->andWhere("$alias.active = 1");
    }

    /**
     * @param $category
     * @param $characteristicValues
     * @param $filters
     * @return array
     */
    public function getFilteredProductsToCategoryQuery($category, $characteristicValues, $filters)
    {
        $builder = $this->createQueryBuilderWithJoins();

        $builder = $this->_em->getRepository('AppBundle:Categories')->addCategoryFilterCondition($builder, $category);

        $builder = $this->_em->getRepository('AppBundle:Products')->addCharacteristicsCondition($builder,
            $characteristicValues, 'productModels');

        $builder = $this->_em->getRepository('AppBundle:Products')->addFiltersToQuery($builder, $filters);

        $builder = $this->_em->getRepository('AppBundle:Products')->addActiveConditionsToQuery($builder);

        $builder = $this->_em->getRepository('AppBundle:ProductModelSpecificSize')
            ->addPriceToQuery($builder, $filters);

        $builder = $this->_em->getRepository('AppBundle:Products')->addSort($builder, Arr::get($filters, 'sort'));

        return $builder->getQuery();
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilderWithJoins()
    {

        return $this->createQueryBuilder('productModels')
            ->select('productModels')
            ->innerJoin('productModels.products', 'products')->addselect('products')
            ->innerJoin('products.baseCategory', 'baseCategory')->addselect('baseCategory')
            ->innerJoin('products.characteristicValues', 'characteristicValues')->addSelect('characteristicValues')
            ->innerJoin('characteristicValues.categories', 'categories')
            ->innerJoin('productModels.productColors', 'productColors')->addselect('productColors')
            ->leftJoin('productModels.images', 'images')->addselect('images')
            ->innerJoin('productModels.sizes', 'sizes')->addselect('sizes')
            ->innerJoin('sizes.size', 'modelSize')->addselect('modelSize')
            ->innerJoin('characteristicValues.characteristics', 'characteristics');
    }

    /**
     * @param array $modelIds
     * @param array $filters
     * @return \Doctrine\ORM\Query
     */
    public function getWishListQuery(array $modelIds, $filters = [])
    {
        $builder = $this->createQueryBuilder('productModels')
            ->innerJoin('productModels.products', 'products')->addselect('products')
            ->innerJoin('products.baseCategory', 'baseCategory')->addselect('baseCategory')
            ->innerJoin('products.characteristicValues', 'characteristicValues')->addSelect('characteristicValues')
            ->innerJoin('characteristicValues.categories', 'categories')
            ->innerJoin('productModels.productColors', 'productColors')->addselect('productColors')
            ->leftJoin('productModels.images', 'images')->addselect('images')
            ->where('productModels.id IN (:ids)')
            ->setParameter('ids', $modelIds)
            ->addOrderBy('FIELD(productModels.id, ' . implode(', ', $modelIds) . ')', 'DESC');

        $builder = $this->_em->getRepository('AppBundle:Products')->addSort($builder, Arr::get($filters, 'sort'));

        return $builder->getQuery();
    }

}
