<?php

namespace AppBundle\Model;

use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\ProductModels;
use AppBundle\Helper\Arr;
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
    public function setSize(ProductModelSpecificSize $size, $quantity)
    {
        if(isset($this->sizes[$size->getId()])) {
            $this->sizes[$size->getId()]->setQuantity($quantity);
        } else {
            $this->addSize($size, $quantity);
        }
        return $this;
    }

    /**
     * @param $size
     * @param $quantity
     * @return $this
     */
    public function incrementSizeQuantity(ProductModelSpecificSize $size, $quantity)
    {
        if(isset($this->sizes[$size->getId()])) {
            $this->sizes[$size->getId()]->incrementQuantity($quantity);
        } else {
            $this->addSize($size, $quantity);
        }
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
    public function getSize(ProductModelSpecificSize $size)
    {
        return isset($this->sizes[$size->getId()]) ? $this->sizes[$size->getId()] : null;
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
     * @return ProductModelSpecificSize[]
     */
    public function getSizesEntities()
    {
        $sizes = [];
        foreach($this->sizes as $size) {
            $sizes[] = $size->getSize();
        }
        return $sizes;
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
                $this->sizes[$newSize->getId()]->incrementQuantity($this->sizes[$oldSize->getId()]->getQuantity());
            } else {
                $cartSize = new CartSize($newSize, $this->pricesCalculator);
                $cartSize->setQuantity($this->sizes[$oldSize->getId()]->getQuantity());
                $this->sizes[$newSize->getId()] = $cartSize;
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
     * @param bool $preOrderFlag
     * @return float
     */
    public function getPackagesDiscountedPrice($preOrderFlag = false)
    {
        return $this->getPackagesQuantity($preOrderFlag) * $this->getPackageDiscountedPrice();
    }

    /**
     * @return int
     */
    public function getSingleItemsQuantity()
    {
        return $this->sumSizesQuantity($this->getSingleItems());
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

    /**
     * @return integer
     */
    public function hasPreOrderSize()
    {
        return (bool)$this->getPreOrderSizesQuantity();
    }

    /**
     * @return integer
     */
    public function getPreOrderSizesQuantity()
    {
        return Arr::sumProperty($this->sizes, 'preOrderQuantity');
    }

    /**
     * @return integer
     */
    public function getStandardSizesQuantity()
    {
        return Arr::sumProperty($this->sizes, 'standardQuantity');
    }

    /**
     * @return integer
     */
    public function isAllSizesPreOrder()
    {
        return !(bool)array_filter($this->sizes, function (CartSize $size) {
            return !$size->getSize()->getPreOrderFlag();
        });
    }

    /**
     * @return float
     */
    public function getStandardPackagesQuantity()
    {
        return $this->getPackagesQuantity();
    }

    /**
     * @return float
     */
    public function getPreOrderPackagesQuantity()
    {
        return $this->getPackagesQuantity(true);
    }

    /**
     * @return float
     */
    public function getAllPackagesQuantity()
    {
        return $this->getStandardPackagesQuantity() + $this->getPreOrderPackagesQuantity();
    }

    /**
     * @param bool $preOrderFlag
     * @return float
     */
    protected function getPackagesQuantity($preOrderFlag = false)
    {
        $availableSizesIds = $this->productModel->getSizes()->map(function ($size) {
            return $size->getId();
        })->toArray();
        $currentSizesIds = array_keys($this->sizes);

        // If array equals in cart all available model sizes.
        if (empty(array_diff($availableSizesIds, $currentSizesIds))) {
            // Packages count - is minimal amount of concrete size.
            // When we have only one size "52-54" - we can`n have more then one package.
            $packagesCount = min(array_map(function (CartSize $size) use($preOrderFlag) {
                return $preOrderFlag ? $size->getPreOrderQuantity() : $size->getStandardQuantity();
            }, $this->sizes));
            return $packagesCount;
        }
        return 0;
    }

    /**
     * @return CartSize[]
     */
    public function getStandardSingleItems()
    {
        return $this->getSingleItems();
    }

    /**
     * @return CartSize[]
     */
    public function getPreOrderSingleItems()
    {
        return $this->getSingleItems(true);
    }

    /**
     * @param bool $preOrderFlag
     * @return CartSize[]
     */
    protected function getSingleItems($preOrderFlag = false)
    {
        $availableSizesIds = $this->productModel->getSizes()->map(function ($size) {
            return $size->getId();
        })->toArray();
        $currentSizesIds = array_keys($this->sizes);

        $quantityGetter = $preOrderFlag ? 'getPreOrderQuantity' : 'getStandardQuantity';

        $singleSizes = [];
        // If array equals in cart all available model sizes.
        if (empty(array_diff($availableSizesIds, $currentSizesIds))) {
            // Packages count - is minimal amount of concrete size.
            // When we have only one size "52-54" - we can`n have more then one package.
            $packagesCount = min(array_map(function ($size) use($quantityGetter) {
                return $size->$quantityGetter();
            }, $this->sizes));
            // All product sizes after $packagesCount - is single items
            foreach ($this->sizes as $sizeId => $size) {
                $singleSizeCount = $size->$quantityGetter() - $packagesCount;
                if ($singleSizeCount) {
                    // Create new size object for new quantity
                    $newSize = new CartSize($size->getSize(), $this->pricesCalculator);
                    $newSize->setQuantity($singleSizeCount);
                    $singleSizes[$sizeId] = $newSize;
                }
            }
        } else {
            foreach ($this->sizes as $sizeId => $size) {
                if ($size->$quantityGetter()) {
                    $newSize = clone $size;
                    $newSize->setQuantity($size->$quantityGetter());
                    $singleSizes[$sizeId] = $newSize;
                }
            }
        }

        return $singleSizes;
    }

    /**
     * @return int
     */
    public function getPreOrderQuantity()
    {
        return array_sum(array_map(function (CartSize $size) {
            return $size->getPreOrderQuantity();
        }, $this->sizes));
    }

    /**
     * @return int
     */
    public function getStandardQuantity()
    {
        return array_sum(array_map(function (CartSize $size) {
            return $size->getStandardQuantity();
        }, $this->sizes));
    }

}
