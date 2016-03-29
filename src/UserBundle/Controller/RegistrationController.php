<?php

namespace UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class RegistrationController
 * @package UserBundle\Controller
 */
class RegistrationController extends BaseController
{

    /**
     * @return RedirectResponse
     */
    public function registerAction()
    {
        $form = $this->container->get('fos_user.registration.form');

        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();

            $this->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate('user_profile');

            $response = new RedirectResponse($url);

            $this->authenticateUser($user, $response);

            return $response;
        }

        return $this->container->get('templating')->renderResponse('AppBundle:user:register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}