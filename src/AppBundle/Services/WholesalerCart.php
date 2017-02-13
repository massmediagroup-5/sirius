<?php

namespace AppBundle\Services;


use AppBundle\Helper\Arr;
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
        return Arr::sumProperty($this->items, 'allPackagesQuantity');
    }

    /**
     * @return int
     */
    public function getSingleItemsCount()
    {
        return $this->getStandardSingleItemsCount() + $this->getPreOrderSingleItemsCount();
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
        return Arr::sumProperty($this->getStandardSingleItems(), 'quantity');
    }

    /**
     * @return int
     */
    public function getPreOrderSingleItemsCount()
    {
        return Arr::sumProperty($this->getPreOrderSingleItems(), 'quantity');
    }

    /**
     * @return CartSize[]
     */
    public function getStandardSingleItems()
    {
        $items = Arr::mapProperty($this->items, 'standardSingleItems');
        return $items ? call_user_func_array('array_merge', $items) : [];
    }

    /**
     * @return CartSize[]
     */
    public function getPreOrderSingleItems()
    {
        $items = Arr::mapProperty($this->items, 'preOrderSingleItems');
        return $items ? call_user_func_array('array_merge', $items) : [];
    }

    /**
     * @return int
     */
    public function getDiscountedTotalPrice()
    {
        $discount = $this->getLoyaltyDiscount();

        return $this->getDiscountedIntermediatePrice() - $discount;
    }

    /**
     * @return int
     */
    public function getLoyaltyDiscountForPreOrder()
    {
        $sum = Arr::sumProperty($this->getSizes(), 'preOrderDiscountedPrice');

        return $this->pricesCalculator->getLoyaltyDiscountForCartForSum($this, $sum);
    }

    /**
     * @return int
     */
    public function getLoyaltyDiscountForStandard()
    {
        $sum = Arr::sumProperty($this->getSizes(), 'standardDiscountedPrice');

        return $this->pricesCalculator->getLoyaltyDiscountForCartForSum($this, $sum);
    }

    /**
     * Return total price for operations with loyalty
     *
     * @return number
     */
    public function getTotalPriceForLoyalty()
    {
        return $this->getDiscountedIntermediatePrice();
    }
}
