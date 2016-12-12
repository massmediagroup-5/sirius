<?php

namespace AppBundle\HistoryItem;


use AppBundle\Entity\OrderHistory;
use AppBundle\Entity\Orders;
use AppBundle\Entity\Users;

/**
 * Class OrderHistoryRelationAddedItem
 * @package AppBundle\HistoryItem
 */
class OrderHistoryRelationAddedItem extends AbstractHistoryItem
{
    /**
     * @param Orders $order
     * @param $relationName
     * @param $entity
     * @param Users $user
     * @return OrderHistory
     */
    public function createHistoryItem(Orders $order, $relationName, $entity, Users $user)
    {
        $historyItem = new OrderHistory();
        $historyItem->setChangeType(get_called_class());
        $historyItem->setChanged($relationName);
        $historyItem->setUser($user);
        $historyItem->setOrder($order);
        if ($relationName == 'sizes') {
            $historyItem->setAdditional([
                'entityId' => $entity->getSize()->getId(),
                'quantity' => $entity->getQuantity()
            ]);
        }
        $order->addHistory($historyItem);
        return $historyItem;
    }

    /**
     * @inheritdoc
     */
    public function makeRollback()
    {
        if ($this->history->getChanged() == 'sizes') {
            $size = $this->em->getReference('AppBundle\Entity\ProductModelSpecificSize',
                $this->history->getAdditional('entityId'));
            $quantity = $this->history->getAdditional('quantity');
            $orderSize = $this->em->getRepository('AppBundle:OrderProductSize')->findOneBySizeAndOrder($size,
                $this->history->getOrder());
            if ($orderSize) {
                $this->container->get('order')->removeSize($this->history->getOrder(), $orderSize, $quantity);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function label()
    {
        if ($this->history->getChanged() == 'sizes') {
            $changedId = $this->history->getAdditional('entityId');
            $size = $this->em->getRepository('AppBundle:ProductModelSpecificSize')->find($changedId);
            return $this->translator->trans('history.order_add_size', [
                ':size' => $size ?: "#{$this->history->getTo()}",
                ':name' => $size ? $size->getModel()->getProducts()->getName() : '#',
                ':article' => $size ? $size->getModel()->getProducts()->getArticle() : "#",
                ':color' => $size ? $size->getModel()->getProductColors()->getName() : "#",
                ':count' => $this->history->getAdditional('quantity'),
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