<?php

namespace AppBundle\Factory;


use AppBundle\Cart\Store\SessionCartStore;
use AppBundle\Services\Cart;
use AppBundle\Services\WholesalerCart;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class CartFactory
 * @package AppBundle\Factory
 * @author R. Slobodzian
 */
class CartFactory
{
    /**
     * @var mixed
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param EntityManager $em
     * @return Cart
     */
    public function createCart(EntityManager $em)
    {
        $security = $this->container->get('security.context');
        $sessionStore = $this->container->get('session_cart_store');
        if ($security->getToken() && $security->isGranted('ROLE_WHOLESALER')) {
            return new WholesalerCart($em, $this->container, $sessionStore);
        }
        return new Cart($em, $this->container, $sessionStore);
    }
}
