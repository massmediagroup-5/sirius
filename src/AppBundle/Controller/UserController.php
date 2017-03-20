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
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
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
        $wishList = $this->container->get('wishlist')->paginate($request->get('page', 1), 9, $request->query->all());

        if($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
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

        $lastAddedBonus = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Orders')
            ->lastAddedBonusAt($this->getUser());

        if ($lastAddedBonus) {
            // время добавления последнего бонуса (неактивированного)
            $lastAddedBonusAt = $lastAddedBonus['updateTime'];

            // время из параметров на которое действительны бонусы
            $paramDeactivateTime = $this->container->get('options')->getParamValue('deactivateBonusesTime');
            // дата активации самого свежего бонуса
            $dateActivationLastBonus = clone $lastAddedBonusAt;
            $dateActivationLastBonus->add(new \DateInterval('P14D'));

            $deactivateBonusesTime = $lastAddedBonusAt->add(new \DateInterval('P' . $paramDeactivateTime . 'D'));

            return $this->render('AppBundle:user/loyal_info.html.twig', compact('bonusesInProcess', 'deactivateBonusesTime', 'dateActivationLastBonus'));
        }
        return $this->render('AppBundle:user/loyal_info.html.twig', ['message' => 'У Вас нет заказов']);
    }

    /**
     * @Route("/user/orders", name="user_orders")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     * @return mixed
     */
    public function ordersAction()
    {
        $orders = $this->container->get('order')->getUserOrders($this->getUser());
        
        return $this->render('AppBundle:user/orders_list.html.twig', compact('orders'));
    }

}
