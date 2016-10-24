<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ReturnProduct;
use AppBundle\Form\Type\ReturnProductType;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ReturnController extends Controller
{
    /**
     * @Route("/page/return/", name="product_return")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $return = new ReturnProduct();
        $form = $this->createForm(ReturnProductType::class, null, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order = $this->getDoctrine()->getManager()->getReference('AppBundle:Orders', $form->get('order_id')->getNormData());
            $return->setOrder($order);
            $return->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($return);
            $em->flush();
            return $this->redirectToRoute('return_success');

        }
        return $this->render('AppBundle:product_return.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/return/success/", name="return_success")
     */
    public function successAction(){
        return $this->render('AppBundle:return_success.html.twig');
    }
}
