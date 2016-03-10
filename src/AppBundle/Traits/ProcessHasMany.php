<?php

namespace AppBundle\Traits;

/**
 * Class ProcessHasMany
 *
 * @package AppBundle\Traits
 */
trait ProcessHasMany {

    /**
     * Set has many relation, sync
     *
     * @param $associationName
     * @param $newCollection
     * @param bool|true $syncFlag
     */
    protected function setHasMany($associationName, $newCollection, $syncFlag = true) {
        // Get new ids
        $ids = [];
        foreach($newCollection as $item) {
            $ids[] = $item->getId();
        }

        // Get old ids
        $oldIds = [];
        foreach($this->$associationName as $item) {
            $oldIds[] = $item->getId();
        }

        // Remove item which not in new collection
        if($syncFlag) {
            foreach($this->$associationName as $key => $item) {
                if(!in_array($item->getId(), $ids)) {
                    $this->$associationName->remove($key);
                }
            }
        }

        // Add new items
        foreach($newCollection as $key => $item) {
            if(!in_array($item->getId(), $oldIds)) {
                $this->$associationName->add($item);
            }
        }

    }

}