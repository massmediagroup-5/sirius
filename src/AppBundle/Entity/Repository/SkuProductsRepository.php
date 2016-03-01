<?php

namespace AppBundle\Entity\Repository;

/**
 * Class: SkuProductsRepository
 *
 * @see \Doctrine\ORM\EntityRepository
 */
class SkuProductsRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * findOneSkuForVendor
     *
     * @param mixed $skuProduct
     * @param mixed $vendor
     *
     * @return mixed
     */
    public function findOneSkuForVendor($skuProduct, $vendor)
    {
        return $this->createQueryBuilder('skuProducts')
            ->select('skuProducts')
            ->innerJoin('skuProducts.vendors', 'vendors')->addSelect('vendors')
            ->where('skuProducts.sku = :skuProducts')
            ->setParameter('skuProducts', $skuProduct)
            ->andWhere('vendors.name = :vendorName')
            ->setParameter('vendorName', $vendor)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

}
