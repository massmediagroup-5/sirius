<?php

namespace AppBundle\Model;

use AppBundle\Entity\SkuProducts;
use AppBundle\Services\PricesCalculator;
use Illuminate\Support\Arr;

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

    /**
     * @var PricesCalculator
     */
    protected $pricesCalculator;

    public function __construct(SkuProducts $skuProducts, PricesCalculator $pricesCalculator)
    {
        $this->skuProduct = $skuProducts;
        $this->pricesCalculator = $pricesCalculator;
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
        if($this->sizesCounts[$sizeId] <= 0) {
            $this->removeSize($sizeId);
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
        return $this->getOneItemPrice() * array_sum($this->sizesCounts);
    }

    /**
     * @return number
     */
    public function getDiscountedPrice()
    {
        return $this->getOneItemDiscountedPrice() * array_sum($this->sizesCounts);
    }

    /**
     * @return SkuProducts
     */
    public function getSkuProduct()
    {
        return $this->skuProduct;
    }

    /**
     * @return int
     */
    public function getPackagesQuantity()
    {
        $availableSizesIds = $this->skuProduct->getProductModels()->getSizes()->map(function ($size) {
            return $size->getId();
        })->toArray();
        $currentSizesIds = array_keys($this->sizesCounts);

        // If array equals in cart all available model sizes.
        if (empty(array_diff($availableSizesIds, $currentSizesIds))) {
            // Packages count - is minimal amount of concrete size.
            // When we have only one size "52-54" - we can`n have more then one package.
            $packagesCount = min($this->sizesCounts);
            return $packagesCount;
        }

        return 0;
    }

    /**
     * @return int
     */
    public function getPackagesDiscountedPrice()
    {
        $sizesInPackageCount = $this->skuProduct->getProductModels()->getSizes()->map(function ($size) {
            return $size->getId();
        })->count();

        return $this->getPackagesQuantity() * $sizesInPackageCount * $this->getOneItemDiscountedPrice();
    }

    /**
     * @return int
     */
    public function getSingleItemsQuantity()
    {
        return array_sum($this->getSingleItems());
    }

    /**
     * @return array[int]
     */
    public function getSingleItems()
    {
        $availableSizesIds = $this->skuProduct->getProductModels()->getSizes()->map(function ($size) {
            return $size->getId();
        })->toArray();
        $currentSizesIds = array_keys($this->sizesCounts);

        $singleSizes = [];
        // If array equals in cart all available model sizes.
        if (empty(array_diff($availableSizesIds, $currentSizesIds))) {
            // Packages count - is minimal amount of concrete size.
            // When we have only one size "52-54" - we can`n have more then one package.
            $packagesCount = min($this->sizesCounts);
            // All product sizes after $packagesCount - is single items
            foreach ($this->sizesCounts as $sizeId => $count) {
                $singleSizeCount = $count - $packagesCount;
                if($singleSizeCount) {
                    $singleSizes[$sizeId] = $singleSizeCount;
                }
            }
        } else {
            return $this->sizesCounts;
        }

        return $singleSizes;
    }

    /**
     * @param $sizeId
     * @return int
     */
    public function getSingleSizeQuantity($sizeId)
    {
        $singleItems = $this->getSingleItems();

        return Arr::get($singleItems, $sizeId, 0);
    }

    /**
     * @param $sizeId
     * @return int
     */
    public function getSingleSizePrice($sizeId)
    {
        return $this->getSingleSizeQuantity($sizeId) * $this->getOneItemPrice();
    }

    /**
     * @param $sizeId
     * @return int
     */
    public function getSingleSizeDiscountedPrice($sizeId)
    {
        return $this->getSingleSizeQuantity($sizeId) * $this->getOneItemDiscountedPrice();
    }

    /**
     * @return float
     */
    public function getOneItemPrice()
    {
        return $this->pricesCalculator->getPrice($this->skuProduct->getProductModels());
    }

    /**
     * @return float
     */
    public function getOneItemDiscountedPrice()
    {
        return $this->pricesCalculator->getDiscountedPrice($this->skuProduct->getProductModels());
    }

}
