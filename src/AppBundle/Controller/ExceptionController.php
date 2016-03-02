<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class: ExceptionController
 *
 * @see Controller
 */
class ExceptionController extends Controller
{

    /**
     * indexAction
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->render('AppBundle:userpart:error.html.twig', array(
            'base_dir'          => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'params'            => $this->get('options')->getParams(),
            'cart'              => $this->get('cart')->getHeaderBasketInfo(),
            'compare'           => $this->get('compare')->getHeaderCompareInfo(),
            'recently_reviewed' => $this->get('entities')->getRecentlyViewed(),
        ));
    }
}
