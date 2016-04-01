<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Class: DefaultController
 *
 * @see Controller
 */
class DefaultController extends Controller
{

    /**
     * indexAction
     * @Route("/", name="homepage", options={"expose"=true})
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function indexAction(Request $request)
    {
        $promo = $this->getDoctrine()->getRepository('AppBundle:MainBanners')->findBy(
            array('active'=>1),
            array('priority'=>'ASC')
        );
        return $this->render('AppBundle:home.html.twig', array(
            'promo'=>$promo
        ));
    }

    /**
     * testAction
     * @Route("/test", name="test")
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function testAction(Request $request)
    {
        return $this->redirectToRoute('homepage');
    }

}
