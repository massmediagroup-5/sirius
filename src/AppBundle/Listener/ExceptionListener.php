<?php

namespace AppBundle\Listener;

use AppBundle\Exception\CartEmptyException;
use AppBundle\Exception\BuyerAccessDeniedException;
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
            if ($event->getRequest()->isXmlHttpRequest()) {
                $event->setResponse(new JsonResponse(['messages' => ['Корзина пуста!']], 422));
            }
        } elseif ($exception instanceof BuyerAccessDeniedException) {
            if ($event->getRequest()->isXmlHttpRequest()) {
                $event->setResponse(new JsonResponse(['messages' => ['Вам запрещено делать заказы!']], 422));
            } else {

            }
        }
    }
}