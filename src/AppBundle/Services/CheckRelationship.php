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
            
        return null;
    }

}
