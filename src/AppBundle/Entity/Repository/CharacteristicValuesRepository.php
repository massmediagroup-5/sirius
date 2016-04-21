<?php

namespace AppBundle\Entity\Repository;
use AppBundle\Entity\CharacteristicValues;

/**
 * Class: CharacteristicValuesRepository
 *
 * @see \Doctrine\ORM\EntityRepository
 */
class CharacteristicValuesRepository extends BaseRepository
{

    /**
     * getValuesByCharacteristic
     *
     * @param mixed $characteristic
     * @param string $characteristicValue
     *
     * @return mixed
     */
    public function getValuesByCharacteristic(
        $characteristic,
        $characteristicValue = ''
    )
    {
        $query_obj = $this->createQueryBuilder('characteristicValues')
            ->select('characteristicValues')
            ->innerJoin('characteristicValues.characteristics', 'characteristics')->addSelect('characteristics')
            ->where("characteristics.name = :characteristic")->setParameter('characteristic', $characteristic)
            ->andWhere("characteristicValues.name = :characteristicValue")
            ->setParameter('characteristicValue', $characteristicValue)
            ->setMaxResults(1)
            ;
        return $query_obj->getQuery()->getOneOrNullResult();
    }

    /**
     * getUnicValuesByProductInCategory
     *
     * We must select all CharacteristicValues for all Products in Category,
     * but only for those Characteristics that defined in ALL Products.
     *
     * @param \AppBundle\Entity\Categories $category
     * @param array $characteristics
     *  Array of Characteristics->id.
     *
     * @return mixed
     */
    public function getUnicValuesByProductInCategory(
        \AppBundle\Entity\Categories $category,
        array $characteristics
    )
    {
        $query_obj = $this->createQueryBuilder('characteristicValues')
            ->select('characteristicValues')
            ->innerJoin('characteristicValues.products', 'products')->addSelect('products')
            ->innerJoin('characteristicValues.characteristics', 'characteristics')->addSelect('characteristics')
            ->innerJoin('characteristicValues.categories', 'categories')->addselect('categories')
            ->where("categories.id = :category")->setParameter('category', $category->getId())
            ->andWhere("characteristics.id IN (:characteristics)")->setParameter('characteristics', $characteristics)
            ->getQuery()
            ->getResult()
            ;

        return $query_obj;
    }

    /**
     * getCharacteristicValuesForCategory
     *
     * @param \AppBundle\Entity\Categories $category
     * @param \AppBundle\Entity\Characteristics $characteristic
     *
     * @return array
     */
    public function getCharacteristicValuesForCategory(
        \AppBundle\Entity\Categories      $category,
        \AppBundle\Entity\Characteristics $characteristic
    )
    {
        $result = $this->createQueryBuilder('characteristicValues')
            ->select('characteristicValues')
            ->innerJoin('characteristicValues.characteristics', 'characteristics')->addSelect('characteristics')
            //->innerJoin('characteristics.categories', 'categories')->addSelect('categories')
            ->innerJoin('characteristicValues.categories', 'categories')->addSelect('categories')
            ->where("categories.id = :category")->setParameter('category', $category->getId())
            ->andwhere("characteristics.id = :characteristic")->setParameter('characteristic', $characteristic->getId())
            ->getQuery()
            ->getResult()
            ;

        return $result;
    }

    /**
     * getCharacteristicValuesForCategory
     *
     * @param \AppBundle\Entity\Categories|string $category
     * @param array $characteristicValues
     * @param array $filters
     *
     * @return array
     */
    public function getAvailableCharacteristicValuesForCategoryProducts($category, $characteristicValues, $filters)
    {
        $builder = $this->createQueryBuilder('characteristicValues')
            ->select('characteristicValues')
            ->innerJoin('characteristicValues.characteristics', 'characteristics')
            ->where('characteristics.inFilter = 1')
        ;

        // Append products query part.
        // Filter only values which has products in given filters and category context.
        // Group values by characteristicId - where (v.id = 1 or v.id = 2) and (v.id = 1 or v.id = 2)
        $qb = $this->_em->createQueryBuilder();
        $productsQueryBuilder = $this->_em->createQueryBuilder(); //$qb->expr()->in()
        $productsQueryBuilder->from('AppBundle:Products', 'products')
            ->innerJoin('products.characteristicValues', 'productCharacteristicValues')
            ->innerJoin('products.productModels', 'productModels')
            ->innerJoin('products.baseCategory', 'baseCategory')
            ->andWhere('productModels.published = 1 AND baseCategory.active = 1')
            ->andWhere($qb->expr()->eq('products.baseCategory', $category->getId()))

            ->innerJoin('productCharacteristicValues.characteristics', 'pCharacteristics')
            ->andWhere($builder->expr()->in("productCharacteristicValues.id", $characteristicValues))

            ->select('COUNT(DISTINCT products.id)')
            ->having('COUNT(DISTINCT pCharacteristics.id) >=
                (
                    SELECT COUNT( DISTINCT incchar.id )
                    FROM \AppBundle\Entity\Characteristics as incchar
                    JOIN incchar.characteristicValues as inccharval
                    WHERE
                    ' . ($characteristicValues ? 'inccharval.id IN (' . implode(',', $characteristicValues) . ') OR ' : '') .
                    'inccharval.id = characteristicValues.id
                )
            ')
        ;

        // Filter price
        $productsQueryBuilder = $this->_em->getRepository('AppBundle:Products')->addFiltersToQuery($productsQueryBuilder, $filters);

        $builder->addSelect("({$productsQueryBuilder->getDQL()}) as products_count")
//            ->having("products_count > 0")
            ;

        $result = $builder->getQuery()
            ->getResult();

        return $result;
    }

}
