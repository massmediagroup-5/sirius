<?php

namespace AppBundle\Widgets;

use AppBundle\Entity\CheckAvailability;
use Doctrine\ORM\EntityManager;
use Proxies\__CG__\AppBundle\Entity\FollowThePrice;
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
     * session
     *
     * @var mixed
     */
    private $session;

    /**
     * __construct
     *
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container, Session $session)
    {
        $this->em = $em;
        $this->container = $container;
        $this->session = $session;
        $this->templating = $container->get('templating');
    }

    /**
     * Get header basket
     *
     * @return mixed
     */
    public function headerCart()
    {
        $cart = $this->session->get('cart_items');
        $wishlist = $this->session->get('wishlist');
        $credit = $this->session->get('credit');
        $spy = $this->session->get('spy');

        $result = array(
            'cart_header_tpl'=> '',
            'cart_items_tpl'=> array(),
            'qty'=>0,
            'total_price'=>0,
            'wishlist_qty'=>0,
            'wishlist_ids'=>array(),
            'credit_ids'=>array(),
            'spy_ids'=>array(),
        );
        if($credit){
            $result['credit_ids'] = $credit;
        }
        if($spy){
            $result['spy_ids'] = $spy;
        }
        if($wishlist){
            $result['wishlist_qty'] = count($wishlist);
            $result['wishlist_ids'] = $wishlist;
        }

        if($cart){
            foreach($cart as $item){
                $item['sku'] = $this->em->getRepository('AppBundle:SkuProducts')->findOneById($item['sku']);
                $result['cart_items_tpl'][] = $this->templating->render(
                    'AppBundle:userpart:cart_item_row.html.twig',
                    array(
                        'item' => $item
                    )
                );
                $price = $item['sku']->getProductModels()->getPrice();
                $qty = $item['quantity'];
                $result['total_price'] += $price * $qty;
                $result['qty'] +=$qty;
            }
        }

        return $this->templating->render('AppBundle:widgets/users/header_cart.html.twig', array(
                'hello' => []
            )
        );
    }


}
