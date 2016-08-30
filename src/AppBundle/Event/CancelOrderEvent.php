<?php

namespace AppBundle\Event;


use AppBundle\Entity\Orders;
use AppBundle\Entity\OrderStatus;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OrderEvent
 * @package AppBundle\Listener
 */
class CancelOrderEvent extends Event
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
     * @var OrderStatus
     */
    protected $lastStatus;

    /**
     * OrderEvent constructor.
     * @param Orders $order
     * @param OrderStatus $lastStatus
     * @param bool $flush
     */
    public function __construct(Orders $order, OrderStatus $lastStatus, $flush = true)
    {
        $this->order = $order;
        $this->flush = $flush;
        $this->lastStatus = $lastStatus;
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

    /**
     * @return OrderStatus
     */
    public function getLastStatus()
    {
        return $this->lastStatus;
    }
}