<?php

namespace AppBundle\Entity;

/**
 * OrderHistory
 */
class OrderHistory extends History
{
    const TYPE_MOVE_TO_ORDER = 'move_to_order';
    const TYPE_MOVE_TO_PRE_ORDER = 'move_to_pre_order';
    const TYPE_MERGED_WITH_PRE_ORDER = 'merged_with_order';
    const TYPE_MERGED_WITH_ORDER = 'merged_with_pre_order';
    const TYPE_PRE_ORDER_TO_ORDER = 'pre_pre_order_to_order';
    const TYPE_ORDER_TO_PRE_ORDER = 'pre_order_to_pre_order';

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
