<?php

namespace AppBundle\Entity\Repository;


use AppBundle\Entity\OrderHistory;
use AppBundle\HistoryItem\OrderHistoryMoveFromSizeItem;
use AppBundle\HistoryItem\OrderHistoryMoveToSizeItem;
use AppBundle\HistoryItem\OrderHistoryRelationAddedItem;
use AppBundle\HistoryItem\OrderHistoryRelationChangedItem;
use AppBundle\HistoryItem\OrderHistoryRelationRemovedItem;

/**
 * Class OrderHistory
 * @package AppBundle\Entity\Repository
 */
class OrderHistoryRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param OrderHistory $history
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countOfNewest(OrderHistory $history)
    {
        return $this->createQueryBuilder('history')
            ->select('COUNT(history.id)')
            ->where('history.changeType = :changeType AND history.createTime > :createTime AND history.order = :order')
            ->setParameters([
                'changeType' => $history->getChangeType(),
                'createTime' => $history->getCreateTime(),
                'order' => $history->getOrder()
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param OrderHistory $history
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countOfNewestWithSameChanged(OrderHistory $history)
    {
        return $this->createQueryBuilder('history')
            ->select('COUNT(history.id)')
            ->where('history.changeType = :changeType AND history.createTime > :createTime AND history.changed = :changed
                AND history.order = :order')
            ->setParameters([
                'changeType' => $history->getChangeType(),
                'createTime' => $history->getCreateTime(),
                'changed' => $history->getChanged(),
                'order' => $history->getOrder()
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param OrderHistory $history
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countOfNewestForRelation(OrderHistory $history)
    {
        return $this->createQueryBuilder('history')
            ->select('COUNT(history.id)')
            ->where('((history.changeType IN (:changeType) AND history.changed = :changed) OR (history.changeType IN ' .
                '(:moveChangeType))) AND history.createTime > :createTime AND history.order = :order')
            ->setParameters([
                'changeType' => [
                    OrderHistoryRelationChangedItem::class,
                    OrderHistoryRelationRemovedItem::class,
                    OrderHistoryRelationAddedItem::class,
                ],
                'moveChangeType' => [
                    OrderHistoryMoveToSizeItem::class,
                    OrderHistoryMoveFromSizeItem::class,
                ],
                'createTime' => $history->getCreateTime(),
                'changed' => $history->getChanged(),
                'order' => $history->getOrder()
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }
}
