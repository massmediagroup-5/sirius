<?php

namespace AppBundle\Entity\Repository;

/**
 * CategoriesRepository
 *
 */
class CategoriesRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * getCategoryInfo
     *
     * @param mixed $category
     *
     * @return mixed
     */
    public function getCategoryInfo($category)
    {
        return $this
            ->createQueryBuilder('cat')
            ->where('cat.alias = :alias')
            ->setParameter('alias', $category)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * getAllActiveCategoriesForMenu
     *
     * @return mixed
     */
    public function getAllActiveCategoriesForMenu()
    {
        return $this
            ->createQueryBuilder('cat')
            ->leftJoin('cat.parrent', 'parrent')->addSelect('parrent')
            ->where('cat.inMenu = 1 AND cat.active = 1 AND cat.id != 1')
            ->add('orderBy', 'cat.parrent ASC')
            ->add('orderBy', 'cat.name ASC')
            ->getQuery()
            ->getArrayResult();
    }

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
        $query_obj = $this->createQueryBuilder('categories')
            ->select('categories')
            ->innerJoin('categories.products', 'products')->addSelect('products')
            ->where('categories.id = :category')->setParameter('category', $catId)
            ->andWhere('products.id = :product')->setParameter('product', $prodId)
            ;
        return $query_obj->getQuery()->getArrayResult();
    }

    /**
     * findCategory
     *
     * @param mixed $prodId
     * @param mixed $catId
     *
     * @return mixed
     */
    public function findBaseCategory($prodId, $catId = null)
    {
        $query_obj = $this->createQueryBuilder('categories')
            ->select('categories')
            ->innerJoin('categories.productsBaseCategories', 'productsBaseCategories')->addSelect('productsBaseCategories')
            ->innerJoin('productsBaseCategories.productsForBaseCategories', 'products')->addSelect('products')
            ;
        if (!is_null($catId)) {
            $query_obj
                ->where('categories.id = :category')
                ->setParameter('category', $catId);
        }
        $query_obj
            ->andWhere('products.id = :product')
            ->setParameter('product', $prodId)
            ;
        return $query_obj->getQuery()->getResult();
    }

    /**
     * getCharacteristics
     *
     * @param mixed $category
     *
     * @return mixed
     */
    public function getCharacteristics($category)
    {
        $queryObj = $this->createQueryBuilder('characteristics')
            ->select('characteristics')
            ->innerJoin('characteristics.characteristicValues', 'characteristicValues')->addSelect('characteristicValues')
            ->innerJoin('characteristicValues.categories', 'categories')->addSelect('categories')
            ->where('categories.id = :category')->setParameter('category', $category)
        ;
        return $queryObj->getQuery()->getResult();
    }

}
