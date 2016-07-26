<?php

namespace AppBundle\Entity;

/**
 * OrderHistory
 */
class OrderHistory extends History
{
    /**
     * @var \AppBundle\Entity\Orders
     */
    private $order;


    /**
     * Set order
     *
     * @param \AppBundle\Entity\Orders $order
     *
     * @return OrderHistory
     */
    public function setOrder(\AppBundle\Entity\Orders $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \AppBundle\Entity\Orders
     */
    public function getOrder()
    {
        return $this->order;
    }
}
