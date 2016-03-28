<?php

namespace AppBundle\Widgets;

use AppBundle\Entity\CheckAvailability;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Class: Cart
 * @author Zimm
 */
class Users
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
     * Get header basket
     *
     * @return mixed
     */
    public function headerCart()
    {
        $cartService = $this->container->get('cart');

        return $this->templating->render('AppBundle:widgets/users/header_cart.html.twig', array(
                'totalQuantity' => $cartService->getTotalCount(),
                'totalPrice' => $cartService->getTotalPrice(),
                'items'
            )
        );
    }

    /**
     * Get header basket
     *
     * @return mixed
     */
    public function headerRegistration()
    {
        return $this->templating->render('AppBundle:widgets/users/header_registration.html.twig', array(
            )
        );
    }

}
