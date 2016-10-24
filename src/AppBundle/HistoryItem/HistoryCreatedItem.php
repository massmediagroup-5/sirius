<?php

namespace AppBundle\HistoryItem;


use AppBundle\Entity\History;
use AppBundle\Entity\ProductModelsHistory;
use AppBundle\Entity\ReturnProductHistory;
use AppBundle\Entity\ProductModels;
use AppBundle\Entity\OrderHistory;
use AppBundle\Entity\ProductModelSpecificSize;

/**
 * Class HistoryCreatedItem
 * @package AppBundle\HistoryItem
 */
class HistoryCreatedItem extends AbstractHistoryItem
{
    /**
     * @param  $historibleEntity
     * @return History
     */
    public function createHistoryItem($historibleEntity)
    {
        $setter = 'set' . ucfirst($this->historyPrefix);
        $historyInstanceName = 'AppBundle\Entity\\' . ucfirst($this->historyPrefix) . 'History';

        $historyItem = new $historyInstanceName();
        $historyItem->setChangeType(get_called_class());
        $historyItem->$setter($historibleEntity);
        $historibleEntity->addHistory($historyItem);

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
        return $this->translator->trans('history.'.$this->getPrefixForLabel().'_created', [], 'AppAdminBundle');
    }
}