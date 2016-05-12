<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\ShareSizesGroup;

/**
 * CategoriesRepository
 *
 */
class ProductModelSpecificSizeRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param \Doctrine\ORM\QueryBuilder $builder
     * @param $filters
     * @param string $alias
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function addPriceToQuery($builder, $filters, $alias = 'sizes', $modelAlias = 'productModels', $productAlias = 'products')
    {
        if (!empty($filters['price_from'])) {
            $builder->andWhere(
                $builder->expr()->gte("COALESCE(NULLIF($alias.price, 0), NULLIF($modelAlias.price, 0), $productAlias.price)", $filters['price_from'])
            );
        }

        if (!empty($filters['price_to'])) {
            $builder->andWhere(
                $builder->expr()->lte("COALESCE(NULLIF($alias.price, 0), NULLIF($modelAlias.price, 0), $productAlias.price)", $filters['price_to'])
            );
        }

        return $builder;
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function findWithModels($ids)
    {
        return $this->createQueryBuilder('modelSizes')
            ->innerJoin('modelSizes.model', 'model')->addselect('model')
            ->andWhere('modelSizes.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param ShareSizesGroup $group
     * @return \Doctrine\ORM\Query
     */
    public function getShareGroupSizes(ShareSizesGroup $group)
    {
        $builder = $this->createQueryBuilder('specificSizes')
            ->innerJoin('specificSizes.model', 'model')->addselect('model')
            ->innerJoin('specificSizes.size', 'size')->addselect('size')
            ->innerJoin('model.products', 'product')->addselect('product')
            ->innerJoin('model.productColors', 'color')->addselect('color')
            ->innerJoin('product.characteristicValues', 'characteristicValues')->addselect('characteristicValues')
            ->innerJoin('characteristicValues.characteristics', 'characteristics')
            ->innerJoin('product.baseCategory', 'category')
            ->addselect('specificSizes');

        $shareRepo = $this->_em->getRepository('AppBundle:Share');

        $shareRepo->addHasGroupCondition($builder, $group);
        $shareRepo->addNotHasGroupExceptGivenCondition($builder, $group);

        return $builder->getQuery()->getResult();
    }

}
