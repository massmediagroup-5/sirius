<?php

namespace UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class RegistrationController
 * @package UserBundle\Controller
 */
class SecurityController extends BaseController
{

    /**
     * @return RedirectResponse
     */
    public function loginAction()
    {
        $request = $this->container->get('request');
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $session = $request->getSession();
        /* @var $session \Symfony\Component\HttpFoundation\Session\Session */

        // get the error if any (works with forward and redirect -- see below)
        if (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return new RedirectResponse($this->container->get('router')->generate('homepage'));
    }

}
