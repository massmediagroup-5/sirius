<?php

namespace AppBundle\Event;


use AppBundle\Entity\Orders;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OrderEvent
 * @package AppBundle\Listener
 */
class OrderEvent extends Event
{
    /**
     * @var Orders
     */
    protected $order;

    /**
     * @var bool
     */
    protected $flush;

    /**
     * OrderEvent constructor.
     * @param Orders $order
     * @param bool $flush
     */
    public function __construct(Orders $order, $flush = true)
    {
        $this->order = $order;
        $this->flush = $flush;
    }

    /**
     * @return Orders
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return boolean
     */
    public function isFlush()
    {
        return $this->flush;
    }

}