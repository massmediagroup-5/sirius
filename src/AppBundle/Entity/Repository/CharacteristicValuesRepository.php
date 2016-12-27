<?php

namespace AppBundle\Entity\Repository;


use Doctrine\ORM\QueryBuilder;

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
     * @param $category
     * @param $characteristicsValuesIds
     * @param $filters
     * @return array
     */
    public function getWithModelsCount($category, $characteristicsValuesIds, $filters) {
        /** @var QueryBuilder $modelsBuilder */
        $modelsBuilder = $this->_em->getRepository('AppBundle:ProductModels')
            ->createFilteredProductsToCategoryBuilder($category, $characteristicsValuesIds, $filters, [
                'characteristics' => ['topLevelValueAlias' => 'value'],
                'skip_order' => true
            ]);

        $modelsBuilder->select('COUNT(productModels)');

        $builder = $this->createQueryBuilder('value')
            ->addSelect('characteristic')
            ->join('value.characteristics', 'characteristic')
            ->addSelect("({$modelsBuilder->getDQL()}) modelsCount");

        foreach ($modelsBuilder->getParameters() as $parameter) {
            $builder->setParameter($parameter->getName(), $parameter->getValue());
        }

        return $builder->getQuery()->getResult();
    }

}
