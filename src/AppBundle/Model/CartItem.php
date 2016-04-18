<?php

namespace AppBundle\Model;

use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\ProductModels;
use AppBundle\Services\PricesCalculator;

class CartItem
{

    /**
     * Array contain size id as key and count as value
     *
     * @var CartSize[]
     */
    protected $sizes = [];

    /**
     * @var ProductModels
     */
    protected $productModel;

    /**
     * @var PricesCalculator
     */
    protected $pricesCalculator;

    public function __construct(ProductModels $productModels, PricesCalculator $pricesCalculator)
    {
        $this->productModel = $productModels;
        $this->pricesCalculator = $pricesCalculator;
    }

    /**
     * @param ProductModelSpecificSize $size
     * @param $quantity
     * @return $this
     */
    public function addSize(ProductModelSpecificSize $size, $quantity = 1)
    {
        if (empty($this->sizes[$size->getId()])) {
            $cartSize = new CartSize($size, $this->pricesCalculator);
            $cartSize->setQuantity($quantity);
            $this->sizes[$size->getId()] = $cartSize;
        } else {
            $this->sizes[$size->getId()]->incrementQuantity($quantity);
        }
        if ($this->sizes[$size->getId()]->getQuantity() <= 0) {
            $this->removeSize($size);
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
        $this->sizes[$size]->setQuantity($quantity);

        return $this;
    }

    /**
     * @param $sizes
     * @return mixed
     */
    public function setSizes(array $sizes)
    {
        return $this->sizes = $sizes;
    }

    /**
     * @param $size
     * @return ProductModelSpecificSize|null
     */
    public function getSize($size)
    {
        return isset($this->sizes[$size]) ? $this->sizes[$size] : null;
    }

    /**
     * @param ProductModelSpecificSize $size
     * @return int
     */
    public function getSizeQuantity(ProductModelSpecificSize $size)
    {
        return isset($this->sizes[$size->getId()]) ? $this->sizes[$size->getId()]->getQuantity() : null;
    }

    /**
     * @param ProductModelSpecificSize $size
     * @return int
     */
    public function removeSize(ProductModelSpecificSize $size)
    {
        unset($this->sizes[$size->getId()]);
        return $this;
    }

    /**
     * @return CartSize[]
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * @param ProductModelSpecificSize $oldSize
     * @param ProductModelSpecificSize $newSize
     * @return $this
     */
    public function changeSize(ProductModelSpecificSize $oldSize, ProductModelSpecificSize $newSize)
    {
        if (isset($this->sizes[$oldSize->getId()])) {
            if (isset($this->sizes[$newSize->getId()])) {
                $this->sizes[$newSize->getId()] += $this->sizes[$oldSize->getId()];
            } else {
                $this->sizes[$newSize->getId()] = $this->sizes[$oldSize->getId()];
            }

            unset($this->sizes[$oldSize->getId()]);
        }
        return $this;
    }

    /**
     * @return number
     */
    public function getQuantity()
    {
        return $this->sumSizesQuantity($this->sizes);
    }

    /**
     * @return number
     */
    public function getPrice()
    {
        return $this->sumSizesPrice($this->sizes);
    }

    /**
     * @return number
     */
    public function getDiscountedPrice()
    {
        return $this->sumSizesDiscountedPrice($this->sizes);
    }

    /**
     * @return ProductModels
     */
    public function getProductModel()
    {
        return $this->productModel;
    }

    /**
     * @return int
     */
    public function getPackagesQuantity()
    {
        $availableSizesIds = $this->productModel->getSizes()->map(function ($size) {
            return $size->getId();
        })->toArray();
        $currentSizesIds = array_keys($this->sizes);

        // If array equals in cart all available model sizes.
        if (empty(array_diff($availableSizesIds, $currentSizesIds))) {
            // Packages count - is minimal amount of concrete size.
            // When we have only one size "52-54" - we can`n have more then one package.
            $packagesCount = min(array_map(function ($size) {
                return $size->getQuantity();
            }, $this->sizes));
            return $packagesCount;
        }
        return 0;
    }

    /**
     * @return int
     */
    public function getPackagePrice()
    {
        return array_sum(array_map(function (ProductModelSpecificSize $size) {
            return $this->pricesCalculator->getPrice($size);
        }, $this->productModel->getSizes()->toArray()));
    }

    /**
     * @return int
     */
    public function getPackageDiscountedPrice()
    {
        return array_sum(array_map(function (ProductModelSpecificSize $size) {
            return $this->pricesCalculator->getDiscountedPrice($size);
        }, $this->productModel->getSizes()->toArray()));
    }

    /**
     * @return int
     */
    public function getPackagesDiscountedPrice()
    {
        return $this->getPackagesQuantity() * $this->getPackageDiscountedPrice();
    }

    /**
     * @return int
     */
    public function getSingleItemsQuantity()
    {
        return $this->sumSizesQuantity($this->getSingleItems());
    }

    /**
     * @return array[int]
     */
    public function getSingleItems()
    {
        $availableSizesIds = $this->productModel->getSizes()->map(function ($size) {
            return $size->getId();
        })->toArray();
        $currentSizesIds = array_keys($this->sizes);

        $singleSizes = [];
        // If array equals in cart all available model sizes.
        if (empty(array_diff($availableSizesIds, $currentSizesIds))) {
            // Packages count - is minimal amount of concrete size.
            // When we have only one size "52-54" - we can`n have more then one package.
            $packagesCount = min(array_map(function ($size) {
                return $size->getQuantity();
            }, $this->sizes));
            // All product sizes after $packagesCount - is single items
            foreach ($this->sizes as $sizeId => $size) {
                $singleSizeCount = $size->getQuantity() - $packagesCount;
                if ($singleSizeCount) {
                    // Create new size object for new quantity
                    $newSize = new CartSize($size->getSize(), $this->pricesCalculator);
                    $newSize->setQuantity($singleSizeCount);
                    $singleSizes[$sizeId] = $newSize;
                }
            }
        } else {
            return $this->sizes;
        }

        return $singleSizes;
    }

    /**
     * @param ProductModelSpecificSize $size
     * @return int
     */
    public function getSingleSizeQuantity(ProductModelSpecificSize $size)
    {
        $singleItems = $this->getSingleItems();

        return isset($singleItems[$size->getId()]) ? $singleItems[$size->getId()]->getQuantity() : 0;
    }

    /**
     * @param ProductModelSpecificSize $size
     * @return int
     */
    public function getSingleSizePrice(ProductModelSpecificSize $size)
    {
        $singleItems = $this->getSingleItems();

        return isset($singleItems[$size->getId()]) ? $singleItems[$size->getId()]->getPrice() : 0;
    }

    /**
     * @param ProductModelSpecificSize $size
     * @return int
     */
    public function getSingleSizeDiscountedPrice(ProductModelSpecificSize $size)
    {
        $singleItems = $this->getSingleItems();

        return isset($singleItems[$size->getId()]) ? $singleItems[$size->getId()]->getDiscountedPrice() : 0;
    }

    /**
     * @param $sizes
     * @return number
     */
    protected function sumSizesQuantity($sizes) {
        return array_sum(array_map(function (CartSize $size) {
            return $size->getQuantity();
        }, $sizes));
    }

    /**
     * @param $sizes
     * @return number
     */
    protected function sumSizesPrice($sizes) {
        return array_sum(array_map(function (CartSize $size) {
            return $size->getPrice();
        }, $sizes));
    }

    /**
     * @param $sizes
     * @return number
     */
    protected function sumSizesDiscountedPrice($sizes) {
        return array_sum(array_map(function (CartSize $size) {
            return $size->getDiscountedPrice();
        }, $sizes));
    }

}
