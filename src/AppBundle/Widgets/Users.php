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
        $request = $this->container->get('request');
        $route = $request->get('_route');
        $notRenderFlag = in_array($route, ['homepage', 'cart_show', 'cart_order_aprove']) || $request->isMethod('POST') && in_array($route, ['cart_order']);

        return $this->templating->render('AppBundle:widgets/users/header_cart.html.twig', [
            'notRenderFlag' => $notRenderFlag
        ]);
    }

    /**
     * Get header basket
     *
     * @return mixed
     */
    public function headerRegistration()
    {
        return $this->templating->render('AppBundle:widgets/users/header_registration.html.twig');
    }

}
