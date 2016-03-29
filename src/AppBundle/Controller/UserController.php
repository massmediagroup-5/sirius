<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class UserController
 * @package AppBundle\Controller
 */
class UserController extends Controller
{

    /**
     * @Route("/user/profile", name="user_profile", options={"expose"=true})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return mixed
     */
    public function profileAction()
    {

        return $this->render('AppBundle:user/profile.html.twig', [
        ]);
    }


}
