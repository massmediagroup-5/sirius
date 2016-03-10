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

    /**
     * Find one product by sky and colors
     *
     * @param $skuProduct
     * @param $color
     * @param $decorationColor
     * @param $vendor
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneBySkuAndColorForVendor($skuProduct, $color, $decorationColor, $vendor)
    {
        return $this->createQueryBuilder('skuProducts')
            ->select('skuProducts')
            ->innerJoin('skuProducts.vendors', 'vendors')->addSelect('vendors')
            ->leftJoin('skuProducts.productModels', 'models')
            ->leftJoin('models.productColors', 'color')
            ->leftJoin('models.decorationColor', 'decorationColor')
            ->where('skuProducts.sku = :skuProducts')
            ->andWhere('color.name = :color')
            ->andWhere('decorationColor.name = :decorationColor')
            ->setParameter('skuProducts', $skuProduct)
            ->setParameter('color', $color ?: null)
            ->setParameter('decorationColor', $decorationColor ?: null)
            ->andWhere('vendors.name = :vendorName')
            ->setParameter('vendorName', $vendor)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

}
