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

            $authUser = false;
            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
            } else {
                $authUser = true;
                $route = 'fos_user_registration_confirmed';
            }

            $this->setFlash('success', 'Спасибо за регистрацию, проверьте Вашу почту и активируйте аккаунт');
            $url = $this->container->get('router')->generate('user_profile');

            $response = new RedirectResponse($url);

            if ($authUser) {
                $this->authenticateUser($user, $response);
            }

            return $response;
        }

        return $this->container->get('templating')->renderResponse('AppBundle:user:register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}