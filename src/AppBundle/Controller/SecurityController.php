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
        $redirectUrl = $this->get('last_urls')->getLastRequestedUrl();
        if ($redirectUrl == $this->get('router')->generate('cart_order')) {
            $provider = $this->getUser()->getOauthProvider();
            $provider = $this->get('humanizer')->oauthProvider($provider);
            $this->addFlash('info', $this->get('translator')->trans('login with social net', ['%net%' => $provider]));
        }
        return $this->redirect($redirectUrl);
    }

    /**
     * @return RedirectResponse
     */
    public function connectServiceAction()
    {
        return $this->redirect($this->get('last_urls')->getLastRequestedUrl());
    }

}
