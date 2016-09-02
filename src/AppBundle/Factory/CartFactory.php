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
     * @param Session $session
     * @return Cart
     */
    public function createCart(EntityManager $em, Session $session)
    {
        $security = $this->container->get('security.context');
        $sessionStore = new SessionCartStore($session);
        if ($security->getToken() && $security->isGranted('ROLE_WHOLESALER')) {
            return new WholesalerCart($em, $this->container, $sessionStore);
        }
        return new Cart($em, $this->container, $sessionStore);
    }
}
