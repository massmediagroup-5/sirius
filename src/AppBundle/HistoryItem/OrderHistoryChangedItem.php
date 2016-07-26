<?php

namespace AppBundle\HistoryItem;


use AppBundle\Entity\OrderHistory;
use AppBundle\Entity\Orders;
use AppBundle\Entity\Users;
use Illuminate\Support\Arr;

/**
 * Class OrderHistoryChangedItem
 * @package AppBundle\HistoryItem
 */
class OrderHistoryChangedItem extends AbstractHistoryItem
{
    /**
     * @var array
     */
    protected $relationsMap = [
        'status' => 'AppBundle:OrderStatus',
        'payStatus' => 'AppBundle:OrderStatusPay',
        'cities' => 'AppBundle:Cities',
        'stores' => 'AppBundle:Stores',
    ];

    /**
     * @param Orders $order
     * @param $fieldName
     * @param $from
     * @param $to
     * @param Users $user
     * @return OrderHistory
     */
    public function createHistoryItem(Orders $order, $fieldName, $from, $to, Users $user)
    {
        if ($this->isRelation($fieldName)) {
            $from = $from ? $from->getId() : null;
            $to = $to ? $to->getId() : null;
        }
        $historyItem = new OrderHistory();
        $historyItem->setChangeType(get_called_class());
        $historyItem->setChanged($fieldName);
        $historyItem->setFrom($from);
        $historyItem->setTo($to);
        $historyItem->setUser($user);
        $historyItem->setOrder($order);
        $order->addHistory($historyItem);
        return $historyItem;
    }

    /**
     * @inheritdoc
     */
    public function makeRollback()
    {
        $order = $this->history->getOrder();

        $setter = 'set' . lcfirst($this->history->getChanged());
        if ($this->isRelation($this->history->getChanged())) {
            $ref = $this->history->getFrom();

            if ($repo = $this->getChangedRepo()) {
                $ref = $ref ? $this->em->getReference($repo, $ref) : null;
                $order->$setter($ref);
            }
        } else {
            $order->$setter($this->history->getFrom());
        }

        $this->em->persist($order);
        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function label()
    {
        $from = $this->history->getFrom();
        $to = $this->history->getTo();

        if ($this->isRelation($this->history->getChanged())) {
            if ($this->getChangedRepo()) {
                $repo = $this->em->getRepository($this->getChangedRepo());
                if ($from) {
                    $from = $repo->find($from) ?: "#{$from}";
                }
                if ($to) {
                    $to = $repo->find($to) ?: "#{$to}";
                }
            }
        }

        return $this->translator->trans('history.field_changed_from_to_by_user', [
            ':field_changed' => $this->translator->trans("history.{$this->history->getChanged()}", [],
                'AppAdminBundle'),
            ':before' => $from,
            ':after' => $to,
            ':user' => $this->history->getUser()->getUsername()
        ],
            'AppAdminBundle'
        );
    }

    /**
     * @return boolean
     */
    public function canRollback()
    {
        // Can rollback only when not have newest updates with same types
        return $this->em->getRepository('AppBundle:OrderHistory')->countOfNewestWithSameChanged($this->history) == 0;
    }

    /**
     * @param $fieldName
     * @return bool
     */
    protected function isRelation($fieldName)
    {
        $relationsNames = $this->em->getClassMetadata('AppBundle:Orders')->getAssociationNames();
        return in_array($fieldName, $relationsNames);
    }

    /**
     * @return string
     */
    protected function getChangedRepo()
    {
        return Arr::get($this->relationsMap, $this->history->getChanged());
    }
}