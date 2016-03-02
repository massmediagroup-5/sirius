<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class: WishlistController
 *
 * @see Controller
 */
class WishlistController extends Controller
{
    
    /**
     * showAction
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function showAction(Request $request)
    {
        return $this->render('AppBundle:userpart:wishlist.html.twig', array(
            'base_dir'          => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'params'            => $this->get('options')->getParams(),
            'cart'              => $this->get('cart')->getHeaderBasketInfo(),
            'compare'           => $this->get('compare')->getHeaderCompareInfo(),
            'recently_reviewed' => $this->get('entities')->getRecentlyViewed(),
        ));
    }

}
