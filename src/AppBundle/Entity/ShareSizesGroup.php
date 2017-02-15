<?php

namespace AppBundle\Entity;

use AppBundle\Helper\Arr;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ShareSizesGroup
 */
class ShareSizesGroup
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $discount = 0;

    /**
     * @var \AppBundle\Entity\Share
     */
    private $share;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $products;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $modelSpecificSizes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $actualModelSpecificSizes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sizes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $characteristicValues;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $colors;

    /**
     * Discounts for sizes in this group
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $discounts;

    /**
     * Discounts for sizes in related groups
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $discountCompanions;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->discounts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->discountCompanions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->modelSpecificSizes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->actualModelSpecificSizes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sizes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->characteristicValues = new \Doctrine\Common\Collections\ArrayCollection();
        $this->colors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set discount
     *
     * @param string $discount
     *
     * @return ShareSizesGroup
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set share
     *
     * @param \AppBundle\Entity\Share $share
     *
     * @return ShareSizesGroup
     */
    public function setShare(\AppBundle\Entity\Share $share = null)
    {
        $this->share = $share;

        return $this;
    }

    /**
     * Get share
     *
     * @return \AppBundle\Entity\Share
     */
    public function getShare()
    {
        return $this->share;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ShareSizesGroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add product
     *
     * @param \AppBundle\Entity\Products $product
     *
     * @return ShareSizesGroup
     */
    public function addProduct(\AppBundle\Entity\Products $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \AppBundle\Entity\Products $product
     */
    public function removeProduct(\AppBundle\Entity\Products $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Add modelSpecificSize
     *
     * @param \AppBundle\Entity\ProductModelSpecificSize $modelSpecificSize
     *
     * @return ShareSizesGroup
     */
    public function addModelSpecificSize(\AppBundle\Entity\ProductModelSpecificSize $modelSpecificSize)
    {
        $this->modelSpecificSizes[] = $modelSpecificSize;

        return $this;
    }

    /**
     * Remove modelSpecificSize
     *
     * @param \AppBundle\Entity\ProductModelSpecificSize $modelSpecificSize
     */
    public function removeModelSpecificSize(\AppBundle\Entity\ProductModelSpecificSize $modelSpecificSize)
    {
        $this->modelSpecificSizes->removeElement($modelSpecificSize);
    }

    /**
     * Get modelSpecificSizes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getModelSpecificSizes()
    {
        return $this->modelSpecificSizes;
    }

    /**
     * Add modelSpecificSize
     *
     * @param \AppBundle\Entity\ProductModelSpecificSize $modelSpecificSize
     *
     * @return ShareSizesGroup
     */
    public function addActualModelSpecificSizes(\AppBundle\Entity\ProductModelSpecificSize $modelSpecificSize)
    {
        $this->actualModelSpecificSizes[] = $modelSpecificSize;

        return $this;
    }

    /**
     * Remove modelSpecificSize
     *
     * @param \AppBundle\Entity\ProductModelSpecificSize $modelSpecificSize
     */
    public function removeActualModelSpecificSizes(\AppBundle\Entity\ProductModelSpecificSize $modelSpecificSize)
    {
        $this->actualModelSpecificSizes->removeElement($modelSpecificSize);
    }

    /**
     * Get actualModelSpecificSizes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActualModelSpecificSizes()
    {
        return $this->actualModelSpecificSizes;
    }

    /**
     * Add size
     *
     * @param $sizes
     *
     * @return ShareSizesGroup
     */
    public function setSizes($sizes)
    {
        if ($sizes instanceof \Doctrine\Common\Collections\Collection) {
            $this->sizes = $sizes;
        } else {
            $this->sizes = new ArrayCollection($sizes);
        }

        return $this;
    }

    /**
     * Add color
     *
     * @param $colors
     *
     * @return ShareSizesGroup
     */
    public function setColors($colors)
    {
        if ($colors instanceof \Doctrine\Common\Collections\Collection) {
            $this->colors = $colors;
        } else {
            $this->colors = new ArrayCollection($colors);
        }

        return $this;
    }

    /**
     * Add characteristicValues
     *
     * @param $characteristicValues
     *
     * @return ShareSizesGroup
     */
    public function setCharacteristicValues($characteristicValues)
    {
        if ($characteristicValues instanceof \Doctrine\Common\Collections\Collection) {
            $this->characteristicValues = $characteristicValues;
        } else {
            $this->characteristicValues = new ArrayCollection($characteristicValues);
        }

        return $this;
    }

    /**
     * Add size
     *
     * @param \AppBundle\Entity\ProductModelSizes $size
     *
     * @return ShareSizesGroup
     */
    public function addSize(\AppBundle\Entity\ProductModelSizes $size)
    {
        $this->sizes[] = $size;

        return $this;
    }

    /**
     * Remove size
     *
     * @param \AppBundle\Entity\ProductModelSizes $size
     */
    public function removeSize(\AppBundle\Entity\ProductModelSizes $size)
    {
        $this->sizes->removeElement($size);
    }

    /**
     * Get sizes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * Add characteristicValue
     *
     * @param \AppBundle\Entity\CharacteristicValues $characteristicValue
     *
     * @return ShareSizesGroup
     */
    public function addCharacteristicValue(\AppBundle\Entity\CharacteristicValues $characteristicValue)
    {
        $this->characteristicValues[] = $characteristicValue;

        return $this;
    }

    /**
     * Remove characteristicValue
     *
     * @param \AppBundle\Entity\CharacteristicValues $characteristicValue
     */
    public function removeCharacteristicValue(\AppBundle\Entity\CharacteristicValues $characteristicValue)
    {
        $this->characteristicValues->removeElement($characteristicValue);
    }

    /**
     * Get characteristicValues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCharacteristicValues()
    {
        return $this->characteristicValues;
    }

    /**
     * Add color
     *
     * @param \AppBundle\Entity\ProductColors $color
     *
     * @return ShareSizesGroup
     */
    public function addColor(\AppBundle\Entity\ProductColors $color)
    {
        $this->colors[] = $color;

        return $this;
    }

    /**
     * Remove color
     *
     * @param \AppBundle\Entity\ProductColors $color
     */
    public function removeColor(\AppBundle\Entity\ProductColors $color)
    {
        $this->colors->removeElement($color);
    }

    /**
     * Get colors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getColors()
    {
        return $this->colors;
    }

    /**
     * Add discount
     *
     * @param \AppBundle\Entity\ShareSizesGroupDiscount $discount
     *
     * @return ShareSizesGroup
     */
    public function addDiscount(\AppBundle\Entity\ShareSizesGroupDiscount $discount)
    {
        $this->discounts[] = $discount;

        return $this;
    }

    /**
     * Remove discount
     *
     * @param \AppBundle\Entity\ShareSizesGroupDiscount $discount
     */
    public function removeDiscount(\AppBundle\Entity\ShareSizesGroupDiscount $discount)
    {
        $this->discounts->removeElement($discount);
    }

    /**
     * Get discounts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * Add discountCompanion
     *
     * @param \AppBundle\Entity\ShareSizesGroupDiscount $discountCompanion
     *
     * @return ShareSizesGroup
     */
    public function addDiscountCompanion(\AppBundle\Entity\ShareSizesGroupDiscount $discountCompanion)
    {
        $this->discountCompanions[] = $discountCompanion;

        return $this;
    }

    /**
     * Remove discountCompanion
     *
     * @param \AppBundle\Entity\ShareSizesGroupDiscount $discountCompanion
     */
    public function removeDiscountCompanion(\AppBundle\Entity\ShareSizesGroupDiscount $discountCompanion)
    {
        $this->discountCompanions->removeElement($discountCompanion);
    }

    /**
     * Get discountCompanions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDiscountCompanions()
    {
        return $this->discountCompanions;
    }

    /**
     * Get discountCompanions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAllDiscounts()
    {
        $discounts = $this->discounts->toArray();

        /** @var ShareSizesGroupDiscount $companion */
        foreach ($this->discountCompanions as $companion) {
            $foundedDiscount = Arr::first($discounts, function (ShareSizesGroupDiscount $discount) use ($companion) {
                return $discount->getShareGroup()->getId() == $companion->getCompanion()->getId();
            });
            if (!$foundedDiscount) {
                $discounts[] = $companion;
            }
        }

        return $discounts;
    }
}
