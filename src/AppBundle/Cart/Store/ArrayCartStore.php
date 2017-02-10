<?php

namespace AppBundle\Cart\Store;


/**
 * Class ArrayCartStore
 *
 * @package AppBundle\Cart\Store
 */
class ArrayCartStore implements CartStoreInterface
{
    /**
     * @var StoreModel[]
     */
    protected $sizes = [];

    /**
     * @inheritdoc
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * @inheritdoc
     */
    public function setSizes(array $sizes)
    {
        $this->sizes = $sizes;
    }
}
