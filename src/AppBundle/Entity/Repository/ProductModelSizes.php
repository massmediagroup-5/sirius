<?php

namespace AppBundle\Entity\Repository;


/**
 * Class ProductModelSizes
 *
 * @package AppBundle\Entity\Repository
 */
class ProductModelSizes extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $category
     * @param array $characteristicsValuesIds
     * @param array $filters
     * @return string
     */
    public function findForFilter($category, $characteristicsValuesIds = [], $filters = [])
    {
        $filteredModelsBuilder = $this->_em->getRepository('AppBundle:ProductModels')
            ->createFilteredProductsToCategoryBuilder($category, $characteristicsValuesIds, $filters, [
                'sizes' => ['topLevelSizesAlias' => 'size'],
                'skip_order' => true
            ])
            ->select('COUNT(productModels)');

        $categoriesRepository = $this->_em->getRepository('AppBundle:Categories');

        $subQueryBuilder = $this->_em->getRepository('AppBundle:ProductModelSpecificSize')
            ->createQueryBuilder('sqSpecificSize')
            ->select('sqSize.id')
            ->join('sqSpecificSize.size', 'sqSize')
            ->join('sqSpecificSize.model', 'sqModel')
            ->join('sqModel.products', 'sqProducts')
            ->join('sqProducts.baseCategory', 'sqCategories');

        // Filter values by selected to category characteristics
        $categoriesRepository->addCategoryFilterCondition($subQueryBuilder, $category, 'sqCategories');

        $builder = $this->createQueryBuilder('size')
            ->addSelect("({$filteredModelsBuilder->getDQL()}) modelsCount")
            ->andWhere($subQueryBuilder->expr()->in('size.id', $subQueryBuilder->getDQL()))
            ->orderBy('size.size');

        foreach ($filteredModelsBuilder->getParameters() as $parameter) {
            $builder->setParameter($parameter->getName(), $parameter->getValue());
        }

        return $builder->getQuery()->getResult();
    }
}
