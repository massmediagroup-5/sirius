<?php

namespace AppBundle\Services;

use AppBundle\Entity\CheckAvailability;
use AppBundle\Entity\SkuProducts;
use Doctrine\ORM\EntityManager;
use Proxies\__CG__\AppBundle\Entity\FollowThePrice;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Class: Cart
 * @author A.Kravchuk
 */
class Cart
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
    private $container;

    /**
     * session
     *
     * @var mixed
     */
    private $session;

    /**
     * @param EntityManager $em
     * @param ContainerInterface $container
     * @param Session $session
     */
    public function __construct(EntityManager $em, ContainerInterface $container, Session $session)
    {
        $this->em = $em;
        $this->container = $container;
        $this->session = $session;
    }

    /**
     * @param $skuProductId
     * @param $quantity
     */
    public function addItemToCard($skuProductId, $quantity)
    {
        $cart = $this->session->get('cart_items', []);

        if (isset($cart[$skuProductId])) {
            $cart[$skuProductId]['quantity'] += $quantity;
        } else {
            $cart[$skuProductId] = array(
                'skuId' => $skuProductId,
                'quantity' => $quantity
            );
        }
        $this->session->set('cart_items', $cart);
    }

    /**
     * @param $skuProductId
     * @return bool
     */
    public function inCart($skuProductId)
    {
        $cart = $this->session->get('cart_items', []);

        return isset($cart[$skuProductId]);
    }



}
