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
                $this->get('users')->updateProfile($user);
            }
        }
        return $this->render('AppBundle:user/profile.html.twig', ['form' => $form->createView()]);
    }

}
