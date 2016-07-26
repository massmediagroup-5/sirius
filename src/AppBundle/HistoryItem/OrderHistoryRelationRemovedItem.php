<?php

namespace AppBundle\HistoryItem;


use AppBundle\Entity\OrderHistory;
use AppBundle\Entity\Orders;
use AppBundle\Entity\Users;

/**
 * Class OrderHistoryRelationRemovedItem
 * @package AppBundle\HistoryItem
 */
class OrderHistoryRelationRemovedItem extends AbstractHistoryItem
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
            $size = $this->em->getRepository('AppBundle:ProductModelSpecificSize')->find($this->history->getAdditional('entityId'));
            $this->container->get('order')->changeSizeCount($this->history->getOrder(), $size,
                $this->history->getAdditional('quantity'), true);
        }
    }

    /**
     * @inheritdoc
     */
    public function label()
    {

        if ($this->history->getChanged() == 'sizes') {
            $size = $this->em->getRepository('AppBundle:ProductModelSpecificSize')
                ->find($this->history->getAdditional('entityId'));

            return $this->translator->trans('history.order_remove_size', [
                ':size' => $size ?: "#",
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