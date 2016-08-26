<?php

namespace AppBundle\Model;

use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\ProductModels;
use AppBundle\Services\PricesCalculator;
use Illuminate\Support\Arr;

class CartSize
{

    /**
     * Array contain size id as key and count as value
     *
     * @var array
     */
    protected $sizesCounts = [];

    /**
     * @var ProductModels
     */
    protected $productModel;

    /**
     * @var PricesCalculator
     */
    protected $pricesCalculator;

    /**
     * @var integer
     */
    protected $quantity = 0;

    /**
     * @param ProductModelSpecificSize $size
     * @param PricesCalculator $pricesCalculator
     */
    public function __construct(ProductModelSpecificSize $size, PricesCalculator $pricesCalculator)
    {
        $this->size = $size;
        $this->pricesCalculator = $pricesCalculator;
    }

    /**
     * @param $quantity
     * @return $this
     */
    public function incrementQuantity($quantity = 1)
    {
        $this->quantity += $quantity;

        return $this;
    }

    /**
     * @param $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @param $quantity
     * @return $this
     */
    public function decrementQuantity($quantity = 1)
    {
        $this->quantity -= $quantity;

        return $this;
    }

    /**
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return integer
     */
    public function getSimpleQuantity()
    {
        return $this->getQuantity() > $this->size->getQuantity() ? $this->size->getQuantity() : $this->getQuantity();
    }

    /**
     * @return integer
     */
    public function getPreOrderQuantity()
    {
        return $this->getQuantity() > $this->size->getQuantity() ? $this->getQuantity() - $this->size->getQuantity() : 0;
    }

    /**
     * @return ProductModelSpecificSize
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return ProductModelSpecificSize
     */
    public function getPrice()
    {
        return $this->pricesCalculator->getPrice($this->size) * $this->quantity;
    }

    /**
     * @return ProductModelSpecificSize
     */
    public function getDiscountedPrice()
    {
        return $this->pricesCalculator->getProductModelSpecificSizeInCartDiscountedPrice($this);
    }

    /**
     * @return float
     */
    public function getPricePerItem()
    {
        return $this->pricesCalculator->getPrice($this->size);
    }

    /**
     * @return float
     */
    public function getDiscountedPricePerItem()
    {
        return $this->pricesCalculator->getDiscountedPrice($this->size);
    }

}
