<?php

namespace AppBundle\Entity\Repository;
use AppBundle\Entity\Categories;

/**
 * CategoriesRepository
 *
 */
class CategoriesRepository extends BaseRepository
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
            ->orderBy('cat.priority', 'ASC')
            ->addOrderBy('cat.name', 'ASC')
            ->addOrderBy('cat.parrent', 'ASC')
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

    /**
     * @param \Doctrine\ORM\QueryBuilder $builder
     * @param $tableAlias
     * @param $category
     * @return mixed
     */
    public function addCategoryFilterCondition($builder, $category, $tableAlias = 'baseCategory')
    {
        if($category instanceof Categories) {
            $category = $category->getId();
            $field = 'id';
        } else {
            $field = 'alias';
        }

        // Suppose that we has  max 3 level nesting of categories
        return $builder->leftJoin("$tableAlias.parrent", $tableAlias . '1')
            ->leftJoin("{$tableAlias}1.parrent", $tableAlias . '2')
            ->andWhere($builder->expr()->orX(
                $builder->expr()->eq("{$tableAlias}.$field",  $builder->expr()->literal($category)),
                $builder->expr()->eq("{$tableAlias}1.$field", $builder->expr()->literal($category)),
                $builder->expr()->eq("{$tableAlias}2.$field", $builder->expr()->literal($category))
            ));

    }

}
