<?php

namespace AppBundle\Listener;


use AppBundle\Event\OrderCreated;
use AppBundle\Event\OrderEvent;
use AppBundle\Event\ShareGroupFiltersUpdated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DecrementSizesQuantity
 * @package AppBundle\Listener
 */
class DecrementSizesQuantity implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface $lastUrls
     */
    protected $container;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'app.order_created' => 'decrementSizesQuantity',
        ];
    }

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param OrderEvent $event
     */
    public function decrementSizesQuantity(OrderEvent $event)
    {
        $this->container->get('product')->updateSizesQuantity($event->getOrder());
    }
}