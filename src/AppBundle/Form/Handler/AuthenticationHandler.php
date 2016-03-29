<?php

namespace AppBundle\Form\Handler;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class AuthenticationHandler
 * @package AppBundle\Form\Handler
 */
class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{

    protected $router;
    protected $security;
    protected $userManager;
    protected $service_container;
    protected $container;

    public function __construct(Container $container, RouterInterface $router)
    {
        $this->router = $router;
        $this->container = $container;

    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        $result = [
            'redirect' => $this->container->get('router')->generate('user_profile')
        ];

        return new JsonResponse($result);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $result = [
            'messages' => [
                $this->container->get('translator')->trans($exception->getMessage(), [], 'FOSUserBundle')
            ]
        ];

        return new JsonResponse($result, 422);
    }
}