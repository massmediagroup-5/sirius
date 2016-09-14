<?php

namespace AppBundle\HistoryItem;

use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelsHistory;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\ProductModelSpecificSizeHistory;
use AppBundle\Entity\Users;

/**
 * Class HistoryChangedItem
 * @package AppBundle\HistoryItem
 */
class HistoryChangedItem extends AbstractHistoryItem
{

    /**
     * @var History
     */
    protected $history;

    /**
     * @param ProductModels $productModels
     * @param $fieldName
     * @param $typeProduct
     * @param $from
     * @param $to
     * @param Users $user
     * @return ProductModelsHistory
     */
    public function createHistoryItem($historibleEntity, $fieldName, $from, $to, Users $user)
    {
        if ($historibleEntity instanceof ProductModels) {
            $setter = 'setProductModels';
        } elseif ($historibleEntity instanceof ProductModelSpecificSize) {
            $setter = 'setProductModelSpecificSize';
        } else {
            throw new \InvalidArgumentException();
        }
        $historyInstanceName = get_class($historibleEntity).'History';

        $historyItem = new $historyInstanceName();
        $historyItem->setChangeType(get_called_class());
        $historyItem->setChanged($fieldName);
        $historyItem->setFrom($from);
        $historyItem->setTo($to);
        $historyItem->setUser($user);
        $historyItem->$setter($historibleEntity);
        $historibleEntity->addHistory($historyItem);

        return $historyItem;
    }

    /**
     * @inheritdoc
     */
    public function makeRollback()
    {
        if ($this->history instanceof ProductModelsHistory) {
            $getter = 'getProductModels';
        } elseif ($this->history instanceof ProductModelSpecificSizeHistory) {
            $getter = 'getProductModelSpecificSize';
        } else {
            throw new \InvalidArgumentException();
        }
        $productModel = $this->history->$getter();
        $setter = 'set' . lcfirst($this->history->getChanged());
        $productModel->$setter($this->history->getFrom());

        $this->em->persist($productModel);
        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function label()
    {
        $from = $this->history->getFrom();
        $to = $this->history->getTo();

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
        //return true;
        // Can rollback only when not have newest updates with same types
        if ($this->history instanceof ProductModelsHistory) {
            $repo = 'AppBundle:ProductModelsHistory';
        } elseif ($this->history instanceof ProductModelSpecificSizeHistory) {
            $repo = 'AppBundle:ProductModelSpecificSizeHistory';
        } else {
            throw new \InvalidArgumentException();
        }

        return $this->em->getRepository($repo)->countOfNewestWithSameChanged($this->history) == 0;
    }

}