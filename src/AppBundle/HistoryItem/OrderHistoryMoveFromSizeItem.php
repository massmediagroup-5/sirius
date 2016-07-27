<?php

namespace AppBundle\HistoryItem;


use AppBundle\Entity\OrderHistory;
use AppBundle\Entity\OrderProductSize;
use AppBundle\Entity\Orders;
use AppBundle\Entity\Users;
use AppBundle\Services\Order;

/**
 * Class OrderHistoryMoveFromSizeItem
 * @package AppBundle\HistoryItem
 */
class OrderHistoryMoveFromSizeItem extends AbstractHistoryItem
{
    /**
     * @param Orders $order
     * @param OrderProductSize $size
     * @param $quantity
     * @param Users $user
     * @return OrderHistory
     */
    public function createHistoryItem(Orders $order, OrderProductSize $size, $quantity, Users $user)
    {
        $historyItem = new OrderHistory();
        $historyItem->setChangeType(get_called_class());
        $historyItem->setChanged('sizes');
        $historyItem->setFrom($size->getQuantity() - $quantity);
        $historyItem->setTo($size->getQuantity());
        $historyItem->setUser($user);
        $historyItem->setOrder($order);
        $historyItem->setAdditional(['sizeId' => $size->getSize()->getId()]);
        $order->addHistory($historyItem);
        return $historyItem;
    }

    /**
     * @inheritdoc
     */
    public function makeRollback()
    {
        /** @var Order $orderService */
        $orderService = $this->container->get('order');
        $order = $this->history->getOrder();
        
        // Find this size in related order
        $size = $this->em->getReference('AppBundle\Entity\ProductModelSpecificSize',
            $this->history->getAdditional('sizeId'));
        $orderSize = $this->em->getRepository('AppBundle:OrderProductSize')->findOneBySizeAndOrder($size,
            $order);
        
        $quantity = $this->history->getFrom() - $this->history->getTo();
        
        $orderService->moveSize($order, $orderSize, $quantity);
    }

    /**
     * @inheritdoc
     */
    public function label()
    {
        $size = $this->em->getRepository('AppBundle:ProductModelSpecificSize')->find($this->history->getAdditional('sizeId'));
        $label = $this->history->getOrder()->getPreOrderFlag() ? 'history.size_moved_from_order' : 'history.size_moved_from_pre_order';
        return $this->translator->trans($label, [
            ':size' => $size ?: "#{$this->history->getAdditional('sizeId')}",
            ':name' => $size ? $size->getModel()->getProducts()->getName() : '#',
            ':article' => $size ? $size->getModel()->getProducts()->getArticle() : "#",
            ':color' => $size ? $size->getModel()->getProductColors()->getName() : "#",
            ':count' => $this->history->getTo() - $this->history->getFrom(),
            ':user' => $this->history->getUser()->getUsername(),
        ], 'AppAdminBundle');
    }

    /**
     * @return boolean
     */
    public function canRollback()
    {
        return $this->em->getRepository('AppBundle:OrderHistory')->countOfNewestForRelation($this->history) == 0;
    }
}