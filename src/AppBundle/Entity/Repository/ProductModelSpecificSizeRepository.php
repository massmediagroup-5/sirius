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
     * @param \Doctrine\ORM\QueryBuilder $builder
     * @param $sizeAlias
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function addActiveConditionsToQuery($builder, $sizeAlias = 'sizes')
    {
        $builder->andWhere($builder->expr()->orX(
            $builder->expr()->gt("$sizeAlias.quantity", 0),
            $builder->expr()->eq("$sizeAlias.preOrderFlag", true)
        ));

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
     * @param $size
     * @return mixed
     */
    public function findOneByProductModelAndSizeName($productModel, $size)
    {
        return $this->createQueryBuilder('modelSizes')
            ->innerJoin('modelSizes.size', 'size')
            ->andWhere('modelSizes.model = :model')
            ->andWhere('size.size = :size')
            ->setParameter('size', $size)
            ->setParameter('model', $productModel)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    /**
     * @param $article
     * @param $color
     * @param $decorationColor
     * @param $size
     * @return mixed
     */
    public function findOneByProductArticleColorAndSizeName($article, $color, $decorationColor, $size)
    {
        $modelsRepository = $this->_em->getRepository('AppBundle:ProductModels');

        return $this->createQueryBuilder('modelSizes')
            ->innerJoin('modelSizes.size', 'size')
            ->innerJoin('modelSizes.model', 'model')
            ->innerJoin('model.products', 'product')
            ->innerJoin('model.productColors', 'color')
            ->leftJoin('model.decorationColor', 'decorationColor')
            ->andWhere('product.article = :article AND color.name = :color AND size.size = :size')
            ->addCriteria($modelsRepository->getDecorationColorCriteria($decorationColor))
            ->setParameter('size', $size)
            ->setParameter('article', $article)
            ->setParameter('color', $color)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
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

    public function isProductModelsIsOrdered($ids)
    {
        return $this->createQueryBuilder('sizes')
            ->innerJoin('sizes.orderedSizes', 'orderedSizes')
            ->innerJoin('sizes.model', 'model')
            ->where('model.id IN (:modelIds)')
            ->setParameter('modelIds', $ids)
            ->select('COUNT(sizes)')
            ->getQuery()
            ->getSingleScalarResult();
    }

}
