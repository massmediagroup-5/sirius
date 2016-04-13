<?php

namespace AppBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;

/**
 * Class RegistrationFormHandler
 * @package AppBundle\Form\Handler
 */
class RegistrationFormHandler extends BaseHandler
{
    /**
     * @param bool|false $confirmation
     * @return bool
     */
    public function process($confirmation = false)
    {
        $user = $this->userManager->createUser();

        $this->form->setData($user);

        if ('POST' == $this->request->getMethod()) {
            $this->form->bind($this->request);
            $user->setUsername($user->getEmail());
            if ($this->form->isValid()) {
                $this->onSuccess($user, $confirmation);
                return true;
            }
        }

        return false;
    }
}