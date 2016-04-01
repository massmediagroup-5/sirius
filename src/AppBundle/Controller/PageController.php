<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class: PageController
 *
 * @see Controller
 */
class PageController extends Controller
{

    /**
     * indexAction
     * @Route("/info/{alias}", name="info")
     *
     * @param mixed $alias
     * @param Request $request
     *
     * @return mixed
     */
    public function indexAction($alias, Request $request)
    {
        $page = $this->getDoctrine()->getRepository('AppBundle:Pages')->findOneByAlias($alias);
        if ($page) {
            $options = $this->get('options');
            return $this->render('AppBundle:page.html.twig', array(
                'base_dir'          => realpath($this->container->getParameter('kernel.root_dir').'/..'),
                'page'              => $page
            ));
        }else{
            throw $this->createNotFoundException();
        }
    }

}
