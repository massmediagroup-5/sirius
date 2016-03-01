<?php

namespace AppBundle\Entity\Repository;

/**
 * ProductsBaseCategoriesRepository
 *
 */
class ProductsBaseCategoriesRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * findCategory
     *
     * @param mixed $prodId
     * @param mixed $catId
     *
     * @return mixed
     */
    public function findCategory($prodId, $catId)
    {
        $query_obj = $this->createQueryBuilder('productsBaseCategories')
            ->select('productsBaseCategories')
            ->innerJoin('productsBaseCategories.categories', 'categories')->addSelect('categories')
            ->innerJoin('productsBaseCategories.products', 'products')->addSelect('products')
            ->where('categories.id = :category')->setParameter('category', $catId)
            ->andWhere('products.id = :product')->setParameter('product', $prodId)
            ;
        return $query_obj->getQuery()->getArrayResult();
    }

}
