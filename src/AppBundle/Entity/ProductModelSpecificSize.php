<?php

namespace AppBundle\Entity;

/**
 * ProductModelSpecificSize
 */
class ProductModelSpecificSize
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $price = 0;

    /**
     * @var string
     */
    private $wholesalePrice = 0;

    /**
     * @var boolean
     */
    private $preOrderFlag = false;

    /**
     * @var integer
     */
    private $quantity = 0;

    /**
     * @var \AppBundle\Entity\ProductModels
     */
    private $model;

    /**
     * @var \AppBundle\Entity\ProductModelSizes
     */
    private $size;

    /**
     * @var \AppBundle\Entity\ShareSizesGroup
     */
    private $shareGroup;

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
     * Set price
     *
     * @param string $price
     *
     * @return ProductModelSpecificSize
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set wholesalePrice
     *
     * @param string $wholesalePrice
     *
     * @return ProductModelSpecificSize
     */
    public function setWholesalePrice($wholesalePrice)
    {
        $this->wholesalePrice = $wholesalePrice;

        return $this;
    }

    /**
     * Get wholesalePrice
     *
     * @return float
     */
    public function getWholesalePrice()
    {
        return $this->wholesalePrice;
    }

    /**
     * Set preOrderFlag
     *
     * @param boolean $preOrderFlag
     *
     * @return ProductModelSpecificSize
     */
    public function setPreOrderFlag($preOrderFlag)
    {
        $this->preOrderFlag = $preOrderFlag;

        return $this;
    }

    /**
     * Get preOrderFlag
     *
     * @return boolean
     */
    public function getPreOrderFlag()
    {
        return $this->preOrderFlag;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return ProductModelSpecificSize
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Decrement quantity
     *
     * @param $quantity
     * @return $this
     * @throws \RuntimeException
     */
    public function decrementQuantity($quantity)
    {
        if ($this->quantity < $quantity) {
            throw new \RuntimeException('Can`t decrement size quantity to minus value');
        }

        $this->quantity -= $quantity;

        return $this;
    }

    /**
     * Set model
     *
     * @param \AppBundle\Entity\ProductModels $model
     *
     * @return ProductModelSpecificSize
     */
    public function setModel(\AppBundle\Entity\ProductModels $model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return \AppBundle\Entity\ProductModels
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set size
     *
     * @param \AppBundle\Entity\ProductModelSizes $size
     *
     * @return ProductModelSpecificSize
     */
    public function setSize(\AppBundle\Entity\ProductModelSizes $size = null)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return \AppBundle\Entity\ProductModelSizes
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $orderedSizes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderedSizes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add orderedSize
     *
     * @param \AppBundle\Entity\OrderProductSize $orderedSize
     *
     * @return ProductModelSpecificSize
     */
    public function addOrderedSize(\AppBundle\Entity\OrderProductSize $orderedSize)
    {
        $this->orderedSizes[] = $orderedSize;

        return $this;
    }

    /**
     * Remove orderedSize
     *
     * @param \AppBundle\Entity\OrderProductSize $orderedSize
     */
    public function removeOrderedSize(\AppBundle\Entity\OrderProductSize $orderedSize)
    {
        $this->orderedSizes->removeElement($orderedSize);
    }

    /**
     * Get orderedSizes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderedSizes()
    {
        return $this->orderedSizes;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->size->getSize();
    }

    /**
     *
     */
    public function __clone()
    {
        $this->id = null;
    }

    /**
     * Set shareGroup
     *
     * @param \AppBundle\Entity\ShareSizesGroup $shareGroup
     *
     * @return ProductModelSpecificSize
     */
    public function setShareGroup(\AppBundle\Entity\ShareSizesGroup $shareGroup = null)
    {
        $this->shareGroup = $shareGroup;

        return $this;
    }

    /**
     * Get shareGroup
     *
     * @return \AppBundle\Entity\ShareSizesGroup
     */
    public function getShareGroup()
    {
        return $this->shareGroup;
    }

    /**
     * @param ShareSizesGroup $shareGroup
     * @return boolean
     */
    public function inShareGroup(ShareSizesGroup $shareGroup)
    {
        return $this->shareGroup && $this->shareGroup->getId() == $shareGroup->getId();
    }

}
