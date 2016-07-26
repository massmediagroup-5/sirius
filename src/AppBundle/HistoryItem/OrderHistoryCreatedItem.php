<?php

namespace AppBundle\HistoryItem;


use AppBundle\Entity\OrderHistory;
use AppBundle\Entity\Orders;

/**
 * Class OrderHistoryCreatedItem
 * @package AppBundle\HistoryItem
 */
class OrderHistoryCreatedItem extends AbstractHistoryItem
{
    /**
     * @param Orders $order
     * @return OrderHistory
     */
    public function createHistoryItem(Orders $order)
    {
        $historyItem = new OrderHistory();
        $historyItem->setChangeType(get_called_class());
        $historyItem->setOrder($order);
        $order->addHistory($historyItem);
        return $historyItem;
    }

    /**
     * @inheritdoc
     */
    public function makeRollback()
    {
    }

    /**
     * @inheritdoc
     */
    public function canRollback()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function label()
    {
        return $this->translator->trans('history.order_created', [], 'AppAdminBundle');
    }
}