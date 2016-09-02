<?php

namespace AppBundle\Cart\Store;


/**
 * Interface CartStoreInterface
 * @package AppBundle\Cart\Store
 */
interface CartStoreInterface
{
    /**
     * @return StoreModel[]
     */
    public function getSizes();

    /**
     * @param array $sizes
     * @return mixed
     */
    public function setSizes(array $sizes);
}
