<?php

namespace AppBundle\Event;


use AppBundle\Entity\Orders;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OrderCreated
 * @package AppBundle\Listener
 */
class OrderCreated extends Event implements OrderEvent
{
    /**
     * @var Orders
     */
    protected $order;

    /**
     * @param Orders $order
     */
    public function __construct(Orders $order)
    {
        $this->order = $order;
    }

    /**
     * @return Orders
     */
    public function getOrder()
    {
        return $this->order;
    }

}