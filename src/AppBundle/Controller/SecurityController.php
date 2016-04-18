<?php

namespace AppBundle\Controller;


use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends BaseController
{
    /**
     * @return RedirectResponse
     */
    public function afterAction()
    {
        return $this->redirect($this->get('last_urls')->getLastRequestedUrl());
    }

    /**
     * @return RedirectResponse
     */
    public function connectServiceAction()
    {
        return $this->redirect($this->get('last_urls')->getLastRequestedUrl());
    }

}
