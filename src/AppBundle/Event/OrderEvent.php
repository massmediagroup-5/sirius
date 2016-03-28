<?php

namespace AppBundle\Event;

use AppBundle\Entity\Orders;
use Symfony\Component\EventDispatcher\Event;

class OrderEvent extends Event
{
    protected $order;

    public function __construct(Orders $order)
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }
}
