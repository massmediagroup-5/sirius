<?php

namespace AppBundle\Factory;


use AppBundle\Cart\Store\CartStoreInterface;
use AppBundle\Entity\Users;
use AppBundle\Services\Cart;
use AppBundle\Services\PricesCalculator;
use AppBundle\Services\WholesalerCart;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * @param CartStoreInterface $cartStore
     *
     * @return Cart|WholesalerCart
     */
    public function createCart(CartStoreInterface $cartStore)
    {
        $token = $this->container->get('security.token_storage')->getToken();
        $user = $token && is_object($token->getUser()) ? $token->getUser() : null;

        return $this->createCartForUser($cartStore, $this->container->get('prices_calculator'), $user);
    }

    /**
     * @param CartStoreInterface $cartStore
     * @param PricesCalculator $priceCalculator
     * @param Users $user
     *
     * @return Cart|WholesalerCart
     */
    public function createCartForUser(
        CartStoreInterface $cartStore,
        PricesCalculator $priceCalculator,
        Users $user = null
    ) {
        $em = $this->container->get('doctrine.orm.entity_manager');

        if ($user && $user->hasRole('ROLE_WHOLESALER')) {
            return new WholesalerCart($em, $this->container, $cartStore, $priceCalculator);
        }

        return new Cart($em, $this->container, $cartStore, $priceCalculator);
    }
}
