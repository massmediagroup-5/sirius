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
        $filteredModelsBuilder = $this->_em->getRepository('AppBundle:ProductModels')
            ->createFilteredProductsToCategoryBuilder($category, $characteristicsValuesIds, $filters, [
                'characteristics' => ['topLevelValueAlias' => 'value'],
                'skip_order' => true
            ])
            ->select('COUNT(productModels)');

        $modelsBuilder = $this->_em->getRepository('AppBundle:ProductModels')
            ->createFilteredProductsToCategoryBuilder($category, [], [], [
                'characteristics' => ['topLevelValueAlias' => 'sqValue'],
                'modelsAlias' => 'allProductModels',
                'sizesAlias' => 'allSizes',
                'prefix' => 'allAvailable',
                'skip_order' => true
            ])
            ->select('1');

        $categoriesRepository = $this->_em->getRepository('AppBundle:Categories');

        $subQueryBuilder = $this->createQueryBuilder('sqValue')
            ->select('sqValue.id')
            ->join('sqValue.characteristics', 'sqCharacteristic')
            ->join('sqCharacteristic.characteristicValues', 'cValue')
            ->join('cValue.categories', 'vCategories')
            ->join('sqCharacteristic.categories', 'cCategories');

        // Filter values by selected to category characteristics
        $categoriesRepository->addCategoryFilterCondition($subQueryBuilder, $category, 'cCategories');

        // Filter values by selected to category values
        $categoriesRepository->addCategoryFilterCondition($subQueryBuilder, $category, 'vCategories');

        // Select only values which have models (not consider filters)
        $subQueryBuilder->andWhere($subQueryBuilder->expr()->exists($modelsBuilder->getDQL()));

        $builder = $this->createQueryBuilder('value')
            ->addSelect('characteristic')
            ->addSelect("({$filteredModelsBuilder->getDQL()}) modelsCount")
            ->join('value.characteristics', 'characteristic')
            ->andWhere($subQueryBuilder->expr()->in('value.id', $subQueryBuilder->getDQL()));

        foreach ($filteredModelsBuilder->getParameters() as $parameter) {
            $builder->setParameter($parameter->getName(), $parameter->getValue());
        }

        return $builder->getQuery()->getResult();
    }

}
