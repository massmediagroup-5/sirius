<?php

namespace AppBundle\Entity\Repository;

/**
 * CategoriesRepository
 *
 */
class ProductModelsRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * getModelsByProductId
     *
     * Get product and category info by their aliases.
     *
     * @param mixed $prodId
     * @param mixed $productModelAlias
     *
     * @return mixed
     */
    public function getModelsByProductId($prodId, $productModelAlias)
    {
        return $this
            ->createQueryBuilder('prodMod')
            ->where('prodMod.products = :prodId AND prodMod.active = 1 AND prodMod.published = 1 AND prodMod.alias != :alias')
            ->setParameter('prodId', $prodId)
            ->setParameter('alias', $productModelAlias)
            ->getQuery()
            ->getResult();
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

        $builder = $this->_em->getRepository('AppBundle:Categories')->addCategoryFilterCondition($builder, $category);

        return $builder->getQuery()->getSingleResult();
    }

}
