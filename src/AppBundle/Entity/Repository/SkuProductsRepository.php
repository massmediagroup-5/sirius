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

    /**
     * getProductInfoByAlias
     *
     * Get product and category info by their aliases.
     *
     * @param mixed $modelAlias
     *
     * @return mixed
     */
    public function getByModelAlias($modelAlias)
    {
        $query_obj = $this->createQueryBuilder('skuProduct')
            ->select('skuProduct')
            ->innerJoin('skuProduct.productModels', 'prodMod')->addselect('prodMod')
            ->innerJoin('prodMod.products', 'prod')->addselect('prod')
            ->innerJoin('prod.actionLabels', 'act')->addselect('act')
            ->innerJoin('prod.characteristicValues', 'prodChVal')->addselect('prodChVal')
            ->innerJoin('prod.baseCategory', 'catb')->addselect('catb')
            ->innerJoin('prodChVal.characteristics', 'prodChName')->addselect('prodChName')
            ->innerJoin('prodMod.productColors', 'prodCol')->addselect('prodCol')
            ->innerJoin('skuProduct.vendors', 'skuProductVnd')->addselect('skuProductVnd')
            ->leftJoin('prodMod.productModelImages', 'prodMImg')->addselect('prodMImg')
            ->where('prod.active = 1 AND prod.published = 1 AND prodMod.active = 1 AND prodMod.published = 1 AND prodMod.alias = :alias')
            ->setParameter('alias', $modelAlias)
            ->orderBy('skuProductVnd.priority', 'ASC')
        ;
        return $query_obj->getQuery()->getSingleResult();
    }

    /**
     * @param $ids
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findWithModels($ids)
    {
        $builder = $this->createQueryBuilder('skuProduct')
            ->select('skuProduct')
            ->innerJoin('skuProduct.productModels', 'productModels')->addselect('productModels')
            ->where('skuProduct.id IN (:ids)')->setParameter('ids', $ids);

        $builder = $this->_em->getRepository('AppBundle:ProductModels')->addEnabledOnSiteConditions($builder);

        return $builder->getQuery()->getResult();
    }

}
