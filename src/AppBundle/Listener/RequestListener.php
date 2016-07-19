<?php

namespace AppBundle\Listener;


use AppBundle\Services\Options;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
    /**
     * @var ContainerInterface $lastUrls
     */
    protected $container;

    /**
     * @var Options $options
     */
    protected $options;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->options = $this->container->get('options');
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $uri = $event->getRequest()->getRequestUri();

        if ($this->options->getParamValue('blockAllSite') && strpos($uri, '/admin') !== 0) {
            $event->getRequest()->attributes->set('_controller',
                'AppBundle\Controller\ExceptionController::disableAction');
        }
    }

}

