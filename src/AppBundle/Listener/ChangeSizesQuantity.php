<?php

namespace AppBundle\Listener;


use AppBundle\Event\CancelOrderEvent;
use AppBundle\Event\OrderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ChangeSizesQuantity
 * @package AppBundle\Listener
 */
class ChangeSizesQuantity implements EventSubscriberInterface
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
            'app.order_accepted' => 'decrementOrderSizesQuantity',
            'app.order_canceled' => 'incrementOrderSizesQuantity',
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
    public function decrementOrderSizesQuantity(OrderEvent $event)
    {
        $this->container->get('product')->decrementSizesQuantity($event->getOrder(), $event->isFlush());
    }

    /**
     * @param CancelOrderEvent $event
     */
    public function incrementOrderSizesQuantity(CancelOrderEvent $event)
    {
        if ($event->getLastStatus()->getCode() != 'new') {
            $this->container->get('product')->incrementSizesQuantity($event->getOrder(), $event->isFlush());
        }
    }
}