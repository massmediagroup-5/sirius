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
        $setter = 'set' . ucfirst($this->historyPrefix);
        $historyInstanceName = 'AppBundle\Entity\\' . ucfirst($this->historyPrefix) . 'History';

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
        $returnName = substr($this->getPrefixForRollBack(), 0 , -7);
        $getter = 'get'.$returnName;

        $returnProduct = $this->history->$getter();
        $setter = 'set'.lcfirst($this->history->getChanged());
        $returnProduct->$setter($this->history->getFrom());

        $this->em->persist($returnProduct);
        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function label()
    {
        $from = $this->history->getFrom() ? $this->history->getFrom() : '0';
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
        // Can rollback only when not have newest updates with same types
        $historyInstanceName = $this->getPrefixForRollBack();

        return $this->em->getRepository('AppBundle:'.$historyInstanceName)->countOfNewestWithSameChanged($this->history) == 0;
    }

}