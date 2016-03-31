<?php

namespace AppBundle\Entity\Repository;

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

        if($productModelAlias) {
            $builder->andWhere('prodMod.alias != :alias')->setParameter('alias', $productModelAlias);
        }

        return $builder->getQuery()->getResult();
    }

    /**
     * Todo complete or remove
     *
     * @param $category
     * @param $filters
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPricesIntervalForFilters($category, $filters)
    {
        $builder = $this
            ->createQueryBuilder('productModels')
            ->select('productModels, MIN(productModels.price) AS min_price, MAX(productModels.price) AS max_price')
            ->innerJoin('productModels.products', 'products')->addselect('products')

            ->innerJoin('products.baseCategory', 'baseCategory')

            ->where('productModels.active = 1 AND productModels.published = 1');

        $builder = $this->addEnabledOnSiteConditions($builder);
        $builder = $this->_em->getRepository('AppBundle:Categories')->addCategoryFilterCondition($builder, $category);

        return $builder->getQuery()->getSingleResult();
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
            ->innerJoin('productModels.skuProducts', 'skuProducts')->addselect('skuProducts')
            ->leftJoin('productModels.productModelImages', 'productModelImages')->addselect('productModelImages')
            // todo fix this hell, doctrine lazy load is slower then over 100 queries
//            ->leftJoin('productModels.sizes', 'sizes')->addselect('sizes')
            ->andWhere('productModels.published = 1 AND productModels.active = 1 AND baseCategory.active = 1')
            ->innerJoin('characteristicValues.characteristics', 'characteristics')
        ;

        $builder = $this->_em->getRepository('AppBundle:Categories')->addCategoryFilterCondition($builder, $category);

        $builder = $this->_em->getRepository('AppBundle:Products')->addCharacteristicsCondition($builder, $characteristicValues);

        $builder = $this->_em->getRepository('AppBundle:Products')->addPriceToQuery($builder, $filters);

        $builder = $this->_em->getRepository('AppBundle:Products')->addSort($builder, Arr::get($filters, 'sort'));

        return $builder->getQuery();
    }

}
