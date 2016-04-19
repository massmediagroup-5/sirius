<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\CharacteristicValues;
use AppBundle\Entity\ProductModelSizes;
use Illuminate\Support\Arr;

/**
 * Class: ProductModelSizesRepository
 *
 * @see \Doctrine\ORM\EntityRepository
 */
class ProductColorsRepository extends BaseRepository
{

    public function getColorsForFilteredProducts($category, $characteristicsValuesIds, $filters)
    {
        // Not filter by selected colors
        unset($filters['colors']);

        $builder = $this->createQueryBuilder('colors')
            ->select('colors')
            ->innerJoin('colors.models', 'productModels')->addselect('productModels')
            ->innerJoin('productModels.products', 'products')->addselect('products')
            ->innerJoin('products.baseCategory', 'baseCategory')->addselect('baseCategory')
            ->innerJoin('products.characteristicValues', 'characteristicValues')->addSelect('characteristicValues')
            ->innerJoin('characteristicValues.categories', 'categories')
            ->innerJoin('productModels.sizes', 'sizes')->addselect('sizes')
            ->andWhere('productModels.active = 1 AND baseCategory.active = 1')
            ->innerJoin('characteristicValues.characteristics', 'characteristics')
            ->orderBy('colors.name', 'ASC');

        $builder = $this->_em->getRepository('AppBundle:Categories')->addCategoryFilterCondition($builder, $category);

        $builder = $this->_em->getRepository('AppBundle:Products')->addCharacteristicsCondition($builder, $characteristicsValuesIds);

        $builder = $this->_em->getRepository('AppBundle:Products')->addFiltersToQuery($builder, $filters);

        return $builder->getQuery()->getResult();
    }
}
