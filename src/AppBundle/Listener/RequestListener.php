<?php

namespace AppBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use AppBundle\Services\LastUrls;

class RequestListener
{
    /**
     * @var ContainerInterface $lastUrls
     */
    protected $container;

    /**
     * @var LastUrls $lastUrls
     */
    protected $lastUrls;

    /**
     * @var array
     */
    protected $routesToRemember = [
        'category',
        'product',
        'homepage',
        'homepage',
        'contacts',
        'cart_show',
        'cart_order',
    ];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->lastUrls = $container->get('last_urls');
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $route = $event->getRequest()->attributes->get('_route');
        if (in_array($route, $this->routesToRemember)) {
            $this->lastUrls->setLastRequestedUrl($event->getRequest()->getRequestUri());
        }
    }

}

