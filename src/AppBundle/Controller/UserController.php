<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\Type\ProfileFormType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package AppBundle\Controller
 */
class UserController extends Controller
{

    /**
     * @Route("/user/profile", name="user_profile", options={"expose"=true})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return mixed
     */
    public function profileAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this
            ->createForm(ProfileFormType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if($this->get('users')->updateProfile($user)) {
                    $this->addFlash(
                        'success',
                        'Ваши данные обновлены'
                    );
                }
            }
        }
        return $this->render('AppBundle:user/profile.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/user/wish_list", name="user_wish_list")
     * @param Request $request
     * @return mixed
     */
    public function wishListAction(Request $request)
    {
        $wishList = $this->container->get('wishlist')->paginate($request->get('page', 1), 8, $request->query->all());

        if($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render('AppBundle:user/wish_list.html.twig', compact('wishList'));
        } else {
            return $this->render('AppBundle:user/logged_out_wish_list.html.twig', compact('wishList'));
        }
    }

    /**
     * @Route("/user/loyal", name="user_loyal")
     * @return mixed
     */
    public function loyalAction()
    {
        if ($this->isGranted('ROLE_WHOLESALER')) {
            return $this->render('AppBundle:user/wholesaler_loyal_info.html.twig');
        }

        $bonusesInProcess = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Orders')
            ->bonusesInProcess($this->getUser());

        return $this->render('AppBundle:user/loyal_info.html.twig', compact('bonusesInProcess'));
    }

    /**
     * @Route("/user/orders", name="user_orders")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return mixed
     */
    public function ordersAction()
    {
        $orders = $this->container->get('order')->getUserOrders($this->getUser());
        
        return $this->render('AppBundle:user/orders_list.html.twig', compact('orders'));
    }

}
