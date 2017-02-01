<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ShareSizesGroup;
use AppBundle\Helper\Arr;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

/**
 * CategoriesRepository
 *
 */
class ProductModelSpecificSizeRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $builder
     * @param $filters
     * @param string $alias
     * @param string $modelAlias
     * @param string $productAlias
     * @param string $shareAlias
     * @param string $shareGroupAlias
     * @return mixed
     */
    public function addPriceToQuery(
        $builder,
        $filters,
        $alias = 'sizes',
        $modelAlias = 'productModels',
        $productAlias = 'products',
        $shareAlias = 'shares',
        $shareGroupAlias = 'shareGroups'
    ) {
        $priceField = $this->getPricePart([
            'shareAlias' => $shareAlias,
            'sizesAlias' => $alias,
            'modelAlias' => $modelAlias,
            'productAlias' => $productAlias,
            'shareGroupAlias' => $shareGroupAlias,
            'wholesaler' => Arr::get($filters, 'wholesaler')
        ]);

        if (!empty($filters['price_from'])) {
            $builder->andWhere($builder->expr()->gte($priceField, $filters['price_from']));
        }

        if (!empty($filters['price_to'])) {
            $builder->andWhere($builder->expr()->lte($priceField, $filters['price_to']));
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
     * @param string $sizeAlias
     * @return Criteria
     */
    public function getActiveCriteria($sizeAlias = 'sizes')
    {
        return Criteria::create()
            ->andWhere(
                Criteria::expr()->orX(
                    Criteria::expr()->gt("$sizeAlias.quantity", 0),
                    Criteria::expr()->eq("$sizeAlias.preOrderFlag", true)
                )
            );
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function findWithModels($ids)
    {
        $builder = $this->createQueryBuilder('sizes')
            ->innerJoin('sizes.model', 'model')->addselect('model')
            ->innerJoin('model.sizes', 'modelSizes')->addselect('modelSizes')
            ->andWhere('sizes.id IN (:ids)')
            ->setParameter('ids', $ids);

        $this->addActiveConditionsToQuery($builder, 'modelSizes');

        return $builder
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

    /**
     * @param QueryBuilder $builder
     * @param $category
     * @param $characteristicValues
     * @param $filters
     * @param string $alias
     * @param $params
     */
    public function applyAvailableSizesCondition(
        QueryBuilder $builder,
        $category,
        $characteristicValues,
        $filters,
        $alias = 'sizes',
        $params = []
    ) {
        $prefix = isset($params['prefix']) ? $params['prefix'] : 'available';
        $params['prefix'] = $prefix;

        $activeSizesBuilder = $this->createAvailableSizesBuilder($category, $characteristicValues, $filters, $params);
        
        $activeSizesBuilder->select('1');
        $activeSizesBuilder->andWhere("{$prefix}sizes.id = $alias.id");

        $builder->andWhere($activeSizesBuilder->expr()->exists($activeSizesBuilder->getDQL()));

        foreach ($activeSizesBuilder->getParameters() as $parameter) {
            $builder->setParameter($parameter->getName(), $parameter->getValue());
        }
    }

    /**
     * @param $category
     * @param $characteristicValues
     * @param $filters
     * @param $params
     * @return QueryBuilder
     */
    public function createAvailableSizesBuilder($category, $characteristicValues, $filters, $params = [])
    {
        $prefix = Arr::get($params, 'prefix');
        $modelsAlias = "{$prefix}productModels";
        $baseCategoryAlias = "{$prefix}baseCategory";
        $productsAlias = "{$prefix}products";
        $characteristicValuesAlias = "{$prefix}characteristicValues";
        $characteristicsAlias = "{$prefix}characteristics";
        $sharesAlias = "{$prefix}share";
        $sizesAlias = "{$prefix}sizes";
        $shareGroupAlias = "{$prefix}shareGroup";

        $builder = $this->createQueryBuilder($sizesAlias)
            ->innerJoin("{$sizesAlias}.model", $modelsAlias)
            ->leftJoin("{$sizesAlias}.shareGroup", $shareGroupAlias)
            ->innerJoin("{$modelsAlias}.products", $productsAlias)
            ->innerJoin("{$productsAlias}.baseCategory", $baseCategoryAlias)
            ->innerJoin("{$productsAlias}.characteristicValues", $characteristicValuesAlias)
            ->leftJoin("$shareGroupAlias.share", $sharesAlias)
            ->innerJoin("{$characteristicValuesAlias}.characteristics", $characteristicsAlias);

        $this->_em->getRepository('AppBundle:Categories')->addCategoryFilterCondition($builder, $category,
            $baseCategoryAlias);

        $this->_em->getRepository('AppBundle:Products')
            ->addCharacteristicsCondition($builder, $characteristicValues, $modelsAlias, $characteristicValuesAlias,
                $characteristicsAlias, Arr::get($params, 'characteristics.topLevelValueAlias'), $prefix);

        $this->_em->getRepository('AppBundle:Products')->addFiltersToQuery($builder, $filters, $modelsAlias,
            $productsAlias, $characteristicValuesAlias, $sharesAlias, $sizesAlias);

        $this->_em->getRepository('AppBundle:Products')->addActiveConditionsToQuery($builder, $modelsAlias,
            $productsAlias);
        $this->_em->getRepository('AppBundle:ProductModelSpecificSize')->addActiveConditionsToQuery($builder,
            $sizesAlias);

        $this->_em->getRepository('AppBundle:ProductModelSpecificSize')
            ->addPriceToQuery($builder, $filters, $sizesAlias, $modelsAlias, $productsAlias, $sharesAlias,
                $shareGroupAlias);

        if (!Arr::get($params, 'skip_order')) {
            $this->_em->getRepository('AppBundle:Products')->addSort($builder, $filters, [
                'productsAlias' => $productsAlias,
                'modelAlias' => $modelsAlias,
                'sizesAlias' => $sizesAlias,
                'shareGroupAlias' => $shareGroupAlias,
                'shareAlias' => $sharesAlias,
                'prefix' => $prefix
            ]);
        }

        return $builder;
    }

    /**
     * @param array $params
     * @return string
     */
    public function getPricePart($params = [])
    {
        $shareAlias = Arr::get($params, 'shareAlias', 'share');
        $sizesAlias = Arr::get($params, 'sizesAlias', 'sizes');
        $modelAlias = Arr::get($params, 'modelAlias', 'models');
        $productAlias = Arr::get($params, 'productAlias', 'products');
        $shareGroupAlias = Arr::get($params, 'shareGroupAlias', 'shareGroups');

        if (Arr::get($params, 'wholesaler')) {
            return "COALESCE(NULLIF($sizesAlias.wholesalePrice, 0), " .
                "NULLIF($modelAlias.wholesalePrice, 0), $productAlias.wholesalePrice)";
        }

        return "IFELSE($shareAlias.groupsCount = 1 AND $shareAlias.status = 1 AND $shareAlias.startTime < NOW() AND $shareAlias.endTime > NOW(), " .
            "COALESCE(NULLIF($sizesAlias.price, 0), NULLIF($modelAlias.price, 0), $productAlias.price) " .
            "* (100 - $shareGroupAlias.discount) * 0.01, " .
            "COALESCE(NULLIF($sizesAlias.price, 0), NULLIF($modelAlias.price, 0), $productAlias.price))";
    }
}
