<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
//use Cocur\Slugify\Slugify;

/**
 * Class: CheckRelationship
 *
 */
class CheckRelationship
{

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * container
     *
     * @var mixed
     */
    protected $container = null;

    /**
     * __construct
     *
     * @param EntityManager $em
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(
        EntityManager $em,
        \Symfony\Component\DependencyInjection\Container $container
    )
    {
        set_time_limit(3600);
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * checkVsProducts
     *
     * Create new CategoriesHasCharacteristicValues and
     * CharacteristicPriorities relationships.
     * Used only for first import!
     *
     * @return null
     */
    public function checkVsProducts()
    {

        $categories = $this->em
            ->getRepository('AppBundle:Categories')
            ->findAll()
            ;

        foreach ($categories as $category) {
            /*
             *  We must select all CharacteristicValues for all Products
             *  in Category, but only for those Characteristics that defined
             *  in ALL Products.
             */
            $characteristics = $this->em
                ->getRepository('AppBundle:Characteristics')
                ->getUnicCharacteristicsByProductInCategory($category);
            $characteristicsArray = $this->container->get('Entities')
                ->getArrayOf('id', $characteristics);

            $chValues = $this->em
                ->getRepository('AppBundle:CharacteristicValues')
                ->getUnicValuesByProductInCategory(
                    $category,
                    $characteristicsArray
                )
                ;
            /*
             *  Prepare arrays Characteristics and CharacteristicValues
             */
            $chValForCategoryIds = $this->container->get('Entities')
                ->getArrayOf('id', $category->getCharacteristicValues()->getValues());
            $characteristicsForCategoryIds = $this->container->get('Entities')
                ->getArrayOf('id', $category->getCharacteristics()->getValues());
            /*
             *Add only new CharacteristicValues to current Category
             */
            foreach ($chValues as $value) {
                if (!in_array($value->getId(), $chValForCategoryIds)) {
                    $category->addCharacteristicValue($value);
                }
            }
            /*
             *Add only new Characteristics to current Category
             */
            foreach ($characteristics as $value) {
                if (!in_array($value->getId(), $characteristicsForCategoryIds)) {
                    $category->addCharacteristic($value);
                }
            }
            $this->em->merge($category);
        }
        $this->em->flush();

        return null;
    }

    /**
     * checkVsCharacteristicValues
     *
     * Create new ProductsHasCategories relationship.
     *
     * @param int $categoryId
     *
     * @return null
     */
    public function checkVsCharacteristicValues($categoryId = 0)
    {
        $allCategories = array();

        // If not isset $categoryId, use all Categories
        if (empty($categoryId)) {
            $allCategories = $this->em
                ->getRepository('AppBundle:Categories')
                ->findAll()
                ;
        } else {
            $allCategories[] = $this->em
                ->getRepository('AppBundle:Categories')
                ->findOneById($categoryId)
                ;
        }

        foreach ($allCategories as $category) {

            /*
             *  We must select all CharacteristicValues for all Products
             *  in Category, but only for those Characteristics that defined
             *  in ALL Products.
             */

            // Get CharacteristicValues for current Category.
            $characteristics = $this->em
                ->getRepository('AppBundle:Characteristics')
                ->getUnicCharacteristicsByCategory($category);
            /*
             *  Get all Products with each CharacteristicValue for
             *  all Characteristics in current Category
             */
            $productsArray = [];
            foreach ($characteristics as $characteristic) {
                $characteristicValues = $this->em
                    ->getRepository('AppBundle:CharacteristicValues')
                    ->getCharacteristicValuesForCategory($category, $characteristic);
                // Get only `id` fields for CharacteristicValues.
                $characteristicsArray = $this->container->get('Entities')
                    ->getArrayOf('id', $characteristicValues);
                $allProducts = $this->em
                    ->getRepository('AppBundle:Products')
                    ->setAllProductsForCategory($category)
                    ->addWhereIn($characteristicsArray)
                    ->getAllProductsForCategory()
                    ;
                // Get only `id` fields for Products.
                $productsArray[] = $this->container->get('Entities')
                    ->getArrayOf('id', $allProducts);
            }
            // Merge arrays.
            $productsArray = (count($productsArray) > 1)
                ? call_user_func_array('array_intersect', $productsArray)
                : array_shift($productsArray);
            // Clear all oldProducts from current Category.
            foreach ($category->getProducts() as $product) {
                $category->removeProduct($product);
                $product->removecategory($category);
                $this->em->persist($category);
                $this->em->persist($product);
            }
            // Add new Products to Category.
            if ($productsArray) {
                foreach ($productsArray as $productId) {
                    $product = $this->em
                        ->getRepository('AppBundle:Products')
                        ->findOneById($productId)
                        ->addCategory($category)
                        ;
                    $this->em->merge($product);
                }
            }
        }
        $this->em->flush();
            
        return null;
    }

}
