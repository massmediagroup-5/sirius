<?php

namespace AppBundle\Widgets;

use AppBundle\Entity\ProductModels;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Class: Products
 * @author Zimm
 */
class Products
{
    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * templating
     *
     * @var mixed
     */
    private $templating;

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * __construct
     *
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
        $this->templating = $container->get('templating');
    }

    /**
     * Render list item
     *
     * @param ProductModels $item
     * @return mixed
     */
    public function wishButton(ProductModels $item)
    {
        $addedFlag = in_array($item->getId(), $this->container->get('wishlist')->getIds());

        return $this->templating->render('AppBundle:widgets/product/wish_button.html.twig', array(
                'item' => $item,
                'addedFlag' => $addedFlag,
            )
        );
    }

    /**
     * Render wish counter
     *
     * @return mixed
     */
    public function wishCounter()
    {
        return $this->templating->render('AppBundle:widgets/product/wish_counter.html.twig', [
                'count' => $this->container->get('wishlist')->getCount(),
            ]
        );
    }

    /**
     * Render recently reviewed
     *
     * @return mixed
     */
    public function recentlyReviewed()
    {
        return $this->templating->render('AppBundle:widgets/product/recently_reviewed.html.twig', [
                // todo select render recently reviewed from database
            ]
        );
    }

    /**
     * Render old and new prices
     *
     * @param $model
     * @return mixed
     */
    public function prices($model)
    {
        return $this->templating->render('AppBundle:widgets/product/prices.html.twig', [
                'model' => $model
            ]
        );
    }


}
