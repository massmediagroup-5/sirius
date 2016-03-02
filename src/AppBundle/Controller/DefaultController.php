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
        return $this->render('AppBundle:home.html.twig');
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
//        return $this->redirectToRoute('homepage');

//        $qweqwe = $this->get('cron_mailer')->FollowThePrice();
//        $data = $this->getDoctrine()->getRepository('AppBundle:Orders')->findOneBy(array(
//            'phone'=> '+38(097)-152-38-54',
//            'type' => 'Обычный заказ'
//        ));
        $em = $this->getDoctrine()->getManager()->getConnection();
        $statement = $em->prepare("SELECT id FROM product_models WHERE active = 1 AND published = 1 ORDER BY RAND() LIMIT 12");
        $statement->execute();
        $results = $statement->fetchAll();
        $results = array_column ($results, 'id');

        $data = $this->getDoctrine()->getRepository('AppBundle:ProductModels')->findBy(
            array('active'=>1,'published'=>1,'id'=>$results),
            array('id'=>'ASC')
        );
        return $this->render('AppBundle:userpart:home.html.twig', array(
            'base_dir'          => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'params'            => $this->get('options')->getParams(),
            'cart'              => $this->get('cart')->getHeaderBasketInfo(),
            'compare'           => $this->get('compare')->getHeaderCompareInfo(),
            'popular'           => $data,
            'recently_reviewed' => $this->get('entities')->getRecentlyViewed(),
        ));
    }

}
