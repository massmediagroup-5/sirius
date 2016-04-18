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
            ->createQueryBuilder('prodMod')
            ->where('prodMod.products = :prodId AND prodMod.active = 1 AND prodMod.published = 1')
            ->setParameter('prodId', $prodId);

        if ($productModelAlias) {
            $builder->andWhere('prodMod.alias != :alias')->setParameter('alias', $productModelAlias);
        }

        return $builder->getQuery()->getResult();
    }

    /**
     * Todo complete or remove
     *
     * @param $category
     * @param $characteristicValues
     * @param $filters
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPricesIntervalForFilters($category, $characteristicValues, $filters)
    {
        $productsSubQuery = $this->getFilteredProductsToCategoryQuery($category, $characteristicValues,
            $filters)->getSQL();
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('min_price', 'min_price');
        $rsm->addScalarResult('max_price', 'max_price');

        return $this->_em->createNativeQuery(
            "SELECT MIN(m.price) AS min_price, MAX(m.price) AS max_price FROM product_models as m INNER JOIN ($productsSubQuery) AS p ON p.id_0 = m.id",
            $rsm
        )->getResult()[0];
    }

    /**
     * @param QueryBuilder $builder
     * @param string $alias
     * @return QueryBuilder
     */
    public function addEnabledOnSiteConditions(QueryBuilder $builder, $alias = 'productModels')
    {
        return $builder->andWhere("$alias.active = 1 AND $alias.published = 1");
    }

    /**
     * @param $category
     * @param $characteristicValues
     * @param $filters
     * @return array
     */
    public function getFilteredProductsToCategoryQuery($category, $characteristicValues, $filters)
    {
        $builder = $this->createQueryBuilder('productModels')
            ->select('productModels');
        // Add a starting Joins.
        $builder
            ->innerJoin('productModels.products', 'products')->addselect('products')
            ->innerJoin('products.baseCategory', 'baseCategory')->addselect('baseCategory')
            ->innerJoin('products.characteristicValues', 'characteristicValues')->addSelect('characteristicValues')
            ->innerJoin('characteristicValues.categories', 'categories')
            ->innerJoin('productModels.productColors', 'productColors')->addselect('productColors')
            ->leftJoin('productModels.images', 'images')->addselect('images')
            ->leftJoin('productModels.sizes', 'sizes')->addselect('sizes')
            ->leftJoin('sizes.size', 'modelSize')->addselect('modelSize')
            ->andWhere('productModels.published = 1 AND productModels.active = 1 AND baseCategory.active = 1')
            ->innerJoin('characteristicValues.characteristics', 'characteristics');

        $builder = $this->_em->getRepository('AppBundle:Categories')->addCategoryFilterCondition($builder, $category);

        $builder = $this->_em->getRepository('AppBundle:Products')->addCharacteristicsCondition($builder,
            $characteristicValues, 'productModels');

        $builder = $this->_em->getRepository('AppBundle:Products')->addFiltersToQuery($builder, $filters);

        $builder = $this->_em->getRepository('AppBundle:ProductModelSpecificSize')
            ->addPriceToQuery($builder, $filters);

        $builder = $this->_em->getRepository('AppBundle:Products')->addSort($builder, Arr::get($filters, 'sort'));

        return $builder->getQuery();
    }

    /**
     * @param array $modelIds
     * @return \Doctrine\ORM\Query
     */
    public function getWishListQuery(array $modelIds)
    {
        return $this->createQueryBuilder('productModels')
            ->innerJoin('productModels.products', 'products')->addselect('products')
            ->innerJoin('products.baseCategory', 'baseCategory')->addselect('baseCategory')
            ->innerJoin('products.characteristicValues', 'characteristicValues')->addSelect('characteristicValues')
            ->innerJoin('characteristicValues.categories', 'categories')
            ->innerJoin('productModels.productColors', 'productColors')->addselect('productColors')
            ->leftJoin('productModels.images', 'images')->addselect('images')
            ->where('productModels.id IN (:ids)')
            ->setParameter('ids', $modelIds)
            ->getQuery();
    }

}
