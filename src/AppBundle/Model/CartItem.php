<?php

namespace AppBundle\Model;

use AppBundle\Entity\SkuProducts;

class CartItem
{

    /**
     * Array contain size id as key and count as value
     *
     * @var array
     */
    protected $sizesCounts = [];

    /**
     * @var SkuProducts
     */
    protected $skuProduct;

    public function __construct(SkuProducts $skuProducts)
    {
        $this->skuProduct = $skuProducts;
    }

    /**
     * @param $sizeId
     * @param $quantity
     * @return $this
     */
    public function addSize($sizeId, $quantity = 1)
    {
        if (empty($this->sizesCounts[$sizeId])) {
            $this->sizesCounts[$sizeId] = $quantity;
        } else {
            $this->sizesCounts[$sizeId] += $quantity;
        }

        return $this;
    }

    /**
     * @param $size
     * @param $quantity
     * @return $this
     */
    public function setSize($size, $quantity)
    {
        $this->sizesCounts[$size] = $quantity;
        return $this;
    }

    /**
     * @param $sizes
     * @return mixed
     */
    public function setSizes(array $sizes)
    {
        return $this->sizesCounts = $sizes;
    }

    /**
     * @param $size
     * @return int
     */
    public function getSize($size)
    {
        return isset($this->sizesCounts[$size]) ? $this->sizesCounts[$size] : 0;
    }

    /**
     * @param $size
     * @return int
     */
    public function removeSize($size)
    {
        unset($this->sizesCounts[$size]);
        return $this;
    }

    /**
     * @return array
     */
    public function getSizes()
    {
        return $this->sizesCounts;
    }

    /**
     * @param $oldSize
     * @param $newSize
     * @return $this
     */
    public function changeSize($oldSize, $newSize)
    {
        if (isset($this->sizesCounts[$oldSize])) {
            if (isset($this->sizesCounts[$newSize])) {
                $this->sizesCounts[$newSize] += $this->sizesCounts[$oldSize];
            } else {
                $this->sizesCounts[$newSize] = $this->sizesCounts[$oldSize];
            }

            unset($this->sizesCounts[$oldSize]);
        }
        return $this;
    }

    /**
     * @return number
     */
    public function getQuantity()
    {
        return array_sum($this->sizesCounts);
    }

    /**
     * @return number
     */
    public function getPrice()
    {
        return $this->skuProduct->getProductModels()->getPrice() * array_sum($this->sizesCounts);
    }

    /**
     * @return number
     */
    public function getOldPrice()
    {
        $price = $this->skuProduct->getProductModels()->getOldPrice() ?: $this->skuProduct->getProductModels()->getPrice();
        return $price * array_sum($this->sizesCounts);
    }

    /**
     * @return SkuProducts
     */
    public function getSkuProduct()
    {
        return $this->skuProduct;
    }

}
