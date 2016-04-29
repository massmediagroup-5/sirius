<?php

namespace AppBundle\Services;

use AppBundle\Model\CartItem;
use AppBundle\Model\CartSize;

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
        return array_sum(array_map(function (CartItem $item) {
            return $item->getPackagesQuantity();
        }, $this->items));
    }

    /**
     * @return int
     */
    public function getSingleItemsCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getSingleItemsQuantity();
        }, $this->items));
    }

    /**
     * @return int
     */
    public function getStandardPackagesCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getPackagesQuantity();
        }, $this->getStandardItems()));
    }

    /**
     * @return int
     */
    public function getPreOrderPackagesCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getPackagesQuantity();
        }, $this->getPreOrderItems()));
    }

    /**
     * @return int
     */
    public function getStandardSingleItemsCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getSingleItemsQuantity();
        }, $this->getStandardItems()));
    }

    /**
     * @return int
     */
    public function getPreOrderSingleItemsCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getSingleItemsQuantity();
        }, $this->getPreOrderItems()));
    }

    /**
     * @return CartItem[]
     */
    public function getStandardItems()
    {
        return array_filter($this->items, function (CartItem $item) {
            return !$item->getProductModel()->isAllSizesPreOrder();
        });
    }

    /**
     * @return CartItem[]
     */
    public function getPreOrderItems()
    {
        return array_filter($this->items, function (CartItem $item) {
            return $item->getProductModel()->isAllSizesPreOrder();
        });
    }

    /**
     * @return CartItem[]
     */
    public function getPreOrderSizes()
    {
        $sizes = array_map(function (CartItem $item) {
            return $item->getSizes();
        }, $this->getPreOrderItems());
        return $sizes ? call_user_func_array('array_merge', $sizes) : [];
    }

    /**
     * @return CartItem[]
     */
    public function getStandardSizes()
    {
        $items = array_map(function (CartItem $item) {
            return $item->getSizes();
        }, $this->getStandardItems());
        return $items ? call_user_func_array('array_merge', $items) : [];
    }

}
