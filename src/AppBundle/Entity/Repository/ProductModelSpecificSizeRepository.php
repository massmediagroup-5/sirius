<?php

namespace AppBundle\Entity\Repository;

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
    public function addPriceToQuery($builder, $filters, $alias = 'sizes')
    {
        if(!empty($filters['price_from'])) {
            $builder->andWhere($builder->expr()->gte("$alias.price", $filters['price_from']));
        }

        if(!empty($filters['price_to'])) {
            $builder->andWhere($builder->expr()->lte("$alias.price", $filters['price_to']));
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

}
