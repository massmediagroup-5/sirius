<?php

namespace AppBundle\Entity\Repository;
use AppBundle\Entity\CharacteristicValues;

/**
 * Class: CharacteristicValuesRepository
 *
 * @see \Doctrine\ORM\EntityRepository
 */
class CharacteristicValuesRepository extends \Doctrine\ORM\EntityRepository
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
            ->innerJoin('products.categories', 'categories')->addSelect('categories')
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
     * @param $searchParameters
     * @param array $createParameters
     * @return CharacteristicValues|array
     */
    public function findOrCreate($searchParameters, $createParameters = [])
    {
        $entity = $this->findOneBy($searchParameters);

        if(!$entity) {
            $entity = new CharacteristicValues();
            $createParameters = array_merge($searchParameters, $createParameters);
            foreach($createParameters as $key => $value) {
                $setter = 'set' . ucfirst($key);
                $entity->$setter($value);
            }
            $this->_em->persist($entity);
            $this->_em->flush();
        }

        return $entity;
    }

}
