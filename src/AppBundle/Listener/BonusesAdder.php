<?php

namespace AppBundle\Listener;


use AppBundle\Event\CancelOrderEvent;
use AppBundle\Services\Order;

/**
 * Class BonusesAdder
 *
 * @package AppBundle\Listener
 */
class BonusesAdder
{
    /**
     * @var Order
     */
    protected $orderService;


    /**
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->orderService = $order;
    }

    /**
     * @param CancelOrderEvent $event
     */
    public function onOrderCanceled(CancelOrderEvent $event)
    {
        if ($event->getOrder()->getUsers() && $event->getLastStatus()->getCode() === 'new') {
            $this->orderService->backBonusesToUser($event->getOrder());
        }
    }
}
