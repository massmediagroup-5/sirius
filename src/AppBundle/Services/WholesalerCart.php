<?php

namespace AppBundle\Services;


use AppBundle\Helper\Arr;

/**
 * Class WholesalerCart
 * @package AppBundle\Services
 * @author R. Slobodzian
 */
class WholesalerCart extends Cart
{
    /**
     * @return int
     */
    public function getPackagesCount()
    {
        return Arr::sumProperty($this->items, 'allPackagesQuantity');
    }

    /**
     * @return int
     */
    public function getSingleItemsCount()
    {
        return Arr::sumProperty($this->items, 'singleItemsQuantity');
    }

    /**
     * @return int
     */
    public function getStandardPackagesCount()
    {
        return Arr::sumProperty($this->items, 'standardPackagesQuantity');
    }

    /**
     * @return int
     */
    public function getPreOrderPackagesCount()
    {
        return Arr::sumProperty($this->items, 'preOrderPackagesQuantity');
    }

    /**
     * @return int
     */
    public function getStandardSingleItemsCount()
    {
        return Arr::sumProperty($this->items, 'standardSingleItems');
    }

    /**
     * @return int
     */
    public function getPreOrderSingleItemsCount()
    {
        return Arr::sumProperty($this->items, 'preOrderSingleItems');
    }
}
