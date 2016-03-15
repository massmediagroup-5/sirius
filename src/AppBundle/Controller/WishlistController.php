<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/wishlist", name="wishlist")
     * @param Request $request
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

    /**
     * @Route("/wishlist/toggle", name="wishlist_toggle", options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleAction(Request $request)
    {
        $this->get('wishlist')->toggle($request->get('model_id'));

        return new JsonResponse(['count' => $this->get('wishlist')->getCount()]);
    }

}
