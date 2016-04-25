<?php

namespace AppBundle\Listener;

use AppBundle\Exception\CartEmptyException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class ExceptionListener
 * @package AppBundle\Listener
 */
class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof CartEmptyException) {
            if($event->getRequest()->isXmlHttpRequest()) {
                $event->setResponse(new JsonResponse(['messages' => ['Корзина пуста!']], 422));
            }
        }
    }
}