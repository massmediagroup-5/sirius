<?php

namespace AppBundle\HistoryItem;


use AppBundle\Entity\OrderHistory;
use AppBundle\Entity\Orders;
use AppBundle\Entity\Users;

/**
 * Class OrderHistoryMergedWithRelatedItem
 * @package AppBundle\HistoryItem
 */
class OrderHistoryMergedWithRelatedItem extends AbstractHistoryItem
{
    /**
     * @param Orders $order
     * @param Users $user
     * @return OrderHistory
     */
    public function createHistoryItem(Orders $order, Users $user)
    {
        $historyItem = new OrderHistory();
        $historyItem->setChangeType(get_called_class());
        $historyItem->setOrder($order);
        $historyItem->setUser($user);
        $historyItem->setAdditional(['preOrder' => $order->getPreOrderFlag()]);
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
        $label = $this->history->getAdditional('preOrder') ? 'merged_with_pre_order' : 'merged_with_order';
        return $this->translator->trans("history.$label", [
            ':user' => $this->history->getUser()->getUsername(),
        ], 'AppAdminBundle');
    }
}
