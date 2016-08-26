<?php

namespace AppBundle\Event;


/**
 * Interface OrderEvent
 * @package AppBundle\Event
 */
interface OrderEvent
{
    /**
     * @return mixed
     */
    public function getOrder();
}