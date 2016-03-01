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

}
