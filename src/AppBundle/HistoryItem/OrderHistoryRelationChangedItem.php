<?php

namespace AppBundle\HistoryItem;


use AppBundle\Entity\OrderHistory;
use AppBundle\Entity\Orders;
use AppBundle\Entity\Users;

/**
 * Class OrderHistoryRelationChangedItem
 * @package AppBundle\HistoryItem
 */
class OrderHistoryRelationChangedItem extends AbstractHistoryItem
{
    /**
     * @param Orders $order
     * @param $relationName
     * @param $entity
     * @param $fieldName
     * @param $from
     * @param $to
     * @param Users $user
     * @return OrderHistory
     */
    public function createHistoryItem(Orders $order, $relationName, $entity, $fieldName, $from, $to, Users $user)
    {
        $historyItem = new OrderHistory();
        $historyItem->setChangeType(get_called_class());
        $historyItem->setChanged($relationName);
        $historyItem->setFrom($from);
        $historyItem->setTo($to);
        $historyItem->setUser($user);
        $historyItem->setOrder($order);
        $historyItem->setAdditional(['fieldName' => $fieldName, 'entityId' => $entity->getId()]);
        $order->addHistory($historyItem);
        return $historyItem;
    }

    /**
     * @inheritdoc
     */
    public function makeRollback()
    {
        $changedField = $this->history->getAdditional('fieldName');
        if ($this->history->getChanged() == 'sizes' && $changedField == 'quantity') {
            $size = $this->em->getReference('AppBundle\Entity\ProductModelSpecificSize',
                $this->history->getAdditional('entityId'));
            if ($size) {
                $quantity = $this->history->getFrom() - $this->history->getTo();
                $this->container->get('order')->incrementSizeCount($this->history->getOrder(), $size, $quantity, true);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function label()
    {
        $changedField = $this->history->getAdditional('fieldName');
        if ($this->history->getChanged() == 'sizes' && $changedField == 'quantity') {
            $changedId = $this->history->getAdditional('entityId');
            $size = $this->em->getRepository('AppBundle:ProductModelSpecificSize')->find($changedId);
            return $this->translator->trans('history.order_update_size', [
                ':size' => $size ?: "#$changedId",
                ':name' => $size ? $size->getModel()->getProducts()->getName() : '#',
                ':article' => $size ? $size->getModel()->getProducts()->getArticle() : "#",
                ':color' => $size ? $size->getModel()->getProductColors()->getName() : "#",
                ':before' => $this->history->getFrom(),
                ':after' => $this->history->getTo(),
                ':user' => $this->history->getUser()->getUsername(),
            ], 'AppAdminBundle');
        }
        return 'Wrong change data...';
    }

    /**
     * @return boolean
     */
    public function canRollback()
    {
        return $this->em->getRepository('AppBundle:OrderHistory')->countOfNewestForRelation($this->history) == 0;
    }
}