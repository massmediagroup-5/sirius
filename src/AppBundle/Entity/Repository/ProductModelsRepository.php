<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\ShareSizesGroup;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Arr;

/**
 * CategoriesRepository
 *
 */
class ProductModelsRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Get product and category info by their aliases.
     *
     * @param mixed $prodId
     * @param mixed $productModelAlias
     *
     * @return mixed
     */
    public function getModelsByProductId($prodId, $productModelAlias = false)
    {
        $builder = $this
            ->createQueryBuilder('productModels')
            ->innerJoin('productModels.products', 'products')->addselect('products')
            ->innerJoin('productModels.sizes', 'sizes')->addselect('sizes')
            ->where('productModels.products = :prodId')
            ->setParameter('prodId', $prodId)
            ->addOrderBy("productModels.priority", 'DESC');

        $builder = $this->_em->getRepository('AppBundle:Products')->addActiveConditionsToQuery($builder);
        $builder = $this->_em->getRepository('AppBundle:ProductModelSpecificSize')->addActiveConditionsToQuery($builder);

        if ($productModelAlias) {
            $builder->andWhere('prodMod.alias != :alias')->setParameter('alias', $productModelAlias);
        }

        return $builder->getQuery()->getResult();
    }

    /**
     * Get product and category info by their aliases.
     *
     * @param $product
     * @param $color
     * @param $decorationColor
     * @return mixed
     */
    public function getModelsByProductAndColors($product, $color, $decorationColor)
    {
        $builder = $this->createQueryBuilder('models')
            ->innerJoin('models.productColors', 'color')
            ->leftJoin('models.decorationColor', 'decorationColor')
            ->innerJoin('models.products', 'products')
            ->where('models.products = :product AND color.name = :color')
            ->setParameters(['product' => $product, 'color' => $color]);

        if ($decorationColor) {
            $builder->andWhere('decorationColor.name = :decorationColor')
                ->setParameter('decorationColor', $decorationColor);
        } else {
            $builder->andWhere('decorationColor.name IS NULL');
        }

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * Get product and category info by their aliases.
     *
     * @param $productArticle
     * @param $color
     * @param $decorationColor
     * @return mixed
     */
    public function getModelsByProductArticleAndColors($productArticle, $color, $decorationColor)
    {
        $builder = $this->createQueryBuilder('models')
            ->innerJoin('models.productColors', 'color')
            ->leftJoin('models.decorationColor', 'decorationColor')
            ->innerJoin('models.products', 'products')
            ->where('products.article = :article AND color.name = :color')
            ->addCriteria($this->getDecorationColorCriteria($decorationColor))
            ->setParameter('article', $productArticle)
            ->setParameter('color', $color);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function getActiveModelsByIds(array $ids)
    {
        $builder = $this
            ->createQueryBuilder('productModels')
            ->innerJoin('productModels.products', 'products')->addselect('products')
            ->innerJoin('productModels.sizes', 'sizes')->addselect('sizes')
            ->where('productModels IN (:ids)')
            ->setParameter('ids', $ids);

        $builder = $this->_em->getRepository('AppBundle:Products')->addActiveConditionsToQuery($builder);
        $builder = $this->_em->getRepository('AppBundle:ProductModelSpecificSize')->addActiveConditionsToQuery($builder);

        return $builder->getQuery()->getResult();
    }

    /**
     * @param $category
     * @param $characteristicValues
     * @param $filters
     * @param bool|false $wholesale
     * @return mixed
     */
    public function getPricesIntervalForFilters($category, $characteristicValues, $filters, $wholesale = false)
    {
        $builder = $this->createQueryBuilderWithJoins();

        $builder = $this->_em->getRepository('AppBundle:Categories')->addCategoryFilterCondition($builder, $category);

        $builder = $this->_em->getRepository('AppBundle:Products')->addCharacteristicsCondition($builder,
            $characteristicValues, 'productModels');

        $builder = $this->_em->getRepository('AppBundle:Products')->addFiltersToQuery($builder, $filters);
        $builder = $this->_em->getRepository('AppBundle:Products')->addActiveConditionsToQuery($builder);
        $builder = $this->_em->getRepository('AppBundle:ProductModelSpecificSize')->addActiveConditionsToQuery($builder);

        $builder->addSelect(
            "MAX(COALESCE(NULLIF(sizes.price, 0), NULLIF(productModels.price, 0), products.price)),
            MIN(COALESCE(NULLIF(sizes.price, 0), NULLIF(productModels.price, 0), products.price))"
        );

        $prices = $builder->getQuery()->getResult();

        return ['max_price' => Arr::get($prices, '0.1', 0), 'min_price' => Arr::get($prices, '0.2', 0)];
    }

    /**
     * @param QueryBuilder $builder
     * @param string $alias
     * @return QueryBuilder
     */
    public function addEnabledOnSiteConditions(QueryBuilder $builder, $alias = 'productModels')
    {
        return $builder->andWhere("$alias.active = 1");
    }

    /**
     * @param $category
     * @param $characteristicValues
     * @param $filters
     * @param $ids
     * @return array
     */
    public function getFilteredProductsToCategoryQuery($category, $characteristicValues, $filters, $ids = array())
    {
        $builder = $this->createQueryBuilderWithJoins();

        if (!empty($ids)) {
            $builder->andWhere("products.id IN(:productsIds)")
                ->setParameter('productsIds', array_values($ids));
        }

        $builder = $this->_em->getRepository('AppBundle:Categories')->addCategoryFilterCondition($builder, $category);

        $builder = $this->_em->getRepository('AppBundle:Products')->addCharacteristicsCondition($builder,
            $characteristicValues, 'productModels');

        $builder = $this->_em->getRepository('AppBundle:Products')->addFiltersToQuery($builder, $filters);

        $builder = $this->_em->getRepository('AppBundle:Products')->addActiveConditionsToQuery($builder);
        $builder = $this->_em->getRepository('AppBundle:ProductModelSpecificSize')->addActiveConditionsToQuery($builder);

        $builder = $this->_em->getRepository('AppBundle:ProductModelSpecificSize')
            ->addPriceToQuery($builder, $filters);

        $builder = $this->_em->getRepository('AppBundle:Products')->addSort($builder, Arr::get($filters, 'sort'));

        return $builder->getQuery();
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilderWithJoins()
    {

        return $this->createQueryBuilder('productModels')
            ->select('productModels')
            ->innerJoin('productModels.products', 'products')->addselect('products')
            ->innerJoin('products.baseCategory', 'baseCategory')->addselect('baseCategory')
            ->innerJoin('products.characteristicValues', 'characteristicValues')->addSelect('characteristicValues')
            ->innerJoin('characteristicValues.categories', 'categories')
            ->innerJoin('productModels.productColors', 'productColors')->addselect('productColors')
            ->leftJoin('productModels.images', 'images')->addselect('images')
            ->innerJoin('productModels.sizes', 'sizes')
            ->leftJoin('sizes.shareGroup', 'shareGroup')
            ->leftJoin('shareGroup.share', 'share')
            ->innerJoin('sizes.size', 'modelSize')
            ->innerJoin('characteristicValues.characteristics', 'characteristics');
    }

    /**
     * @param array $modelIds
     * @param array $filters
     * @return \Doctrine\ORM\Query
     */
    public function getWishListQuery(array $modelIds, $filters = [])
    {
        $builder = $this->createQueryBuilder('productModels')
            ->innerJoin('productModels.products', 'products')->addselect('products')
            ->innerJoin('productModels.sizes', 'sizes')->addselect('sizes')
            ->innerJoin('products.baseCategory', 'baseCategory')->addselect('baseCategory')
            ->leftJoin('products.characteristicValues', 'characteristicValues')->addSelect('characteristicValues')
            ->leftJoin('characteristicValues.categories', 'categories')
            ->innerJoin('productModels.productColors', 'productColors')->addselect('productColors')
            ->leftJoin('productModels.images', 'images')->addselect('images')
            ->where('productModels.id IN (:ids)')
            ->setParameter('ids', $modelIds);

        $builder = $this->_em->getRepository('AppBundle:Products')->addSort($builder, Arr::get($filters, 'sort'));

        $builder->addOrderBy('FIELD(productModels.id, ' . implode(', ', $modelIds) . ')', 'DESC');

        return $builder->getQuery();
    }

    /**
     * @param $filters
     * @return mixed
     */
    public function getAdminSearchQuery($filters = [])
    {
        $builder = $this->createQueryBuilder('model')
            ->leftJoin('model.sizes', 'specificSizes')->addselect('specificSizes')
            ->leftJoin('specificSizes.size', 'size')->addselect('size')
            ->leftJoin('model.productColors', 'color')->addselect('color')
            ->innerJoin('model.products', 'products')->addselect('products')
            ->leftJoin('products.characteristicValues', 'characteristicValues')->addSelect('characteristicValues')
            ->leftJoin('characteristicValues.characteristics', 'characteristics')
            ->leftJoin('products.baseCategory', 'category')
            ->addselect('model');

        if ($categoryId = Arr::get($filters, 'category_id')) {
            $builder->andWhere('category = :categoryId')->setParameter('categoryId', $categoryId);
        }

        if ($size = Arr::get($filters, 'size')) {
            $builder->andWhere('size.size LIKE :size')->setParameter('size', "%$size%");
        }

        if ($article = Arr::get($filters, 'article')) {
            $builder->andWhere('product.article LIKE :article')->setParameter('article', "%$article%");
        }

        if ($color = Arr::get($filters, 'color')) {
            $builder->andWhere('color.name LIKE :color')->setParameter('color', "%$color%");
        }

        if ($model = Arr::get($filters, 'model')) {
            $builder->andWhere('product.name LIKE :model')->setParameter('model', "%$model%");
        }

        if ($model = Arr::get($filters, 'conflicts')) {
            if (!$group = Arr::get($filters, 'group')) {
                throw new \InvalidArgumentException('Provide "group" in filters array');
            }
            $this->_em->getRepository('AppBundle:Share')->addHasGroupCondition($builder, $group);
            $this->_em->getRepository('AppBundle:Share')->addHasGroupExceptGivenCondition($builder, $group);
        }

        if (Arr::get($filters, 'actual')) {
            $this->_em->getRepository('AppBundle:ProductModelSpecificSize')
                ->addPriceToQuery($builder, $filters);
        }

        return $builder->getQuery();
    }

    /**
     * @param ShareSizesGroup $group
     * @return \Doctrine\ORM\Query
     */
    public function getAdminSharesQuery(ShareSizesGroup $group)
    {
        $builder = $this->createQueryBuilder('model')
            ->innerJoin('model.sizes', 'specificSizes')->addselect('specificSizes')
            ->innerJoin('specificSizes.size', 'size')->addselect('size')
            ->innerJoin('model.products', 'product')->addselect('product')
            ->innerJoin('model.productColors', 'color')->addselect('color')
            ->innerJoin('product.characteristicValues', 'characteristicValues')->addselect('characteristicValues')
            ->innerJoin('characteristicValues.characteristics', 'characteristics')
            ->innerJoin('product.baseCategory', 'category')
            ->addselect('model');

        $this->_em->getRepository('AppBundle:Share')->addHasGroupCondition($builder, $group, true);

        return $builder->getQuery();
    }

    /**
     * @param $decorationColor
     * @param string $alias
     * @return Criteria
     */
    public function getDecorationColorCriteria($decorationColor, $alias = 'decorationColor')
    {
        if ($decorationColor) {
            return Criteria::create()->where(Criteria::expr()->eq("$alias.name", $decorationColor));
        } else {
            return Criteria::create()->where(Criteria::expr()->isNull("$alias.name"));
        }
    }
}
