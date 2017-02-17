<?php

namespace AppBundle\Entity;

use AppBundle\Helper\Arr;
use Doctrine\Common\Collections\Collection;

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
     * @var float
     */
    private $oldPrice = 0.0;

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
     * @var ShareSizesGroup
     */
    private $shareGroup;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $shareGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $history;

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
     * @return boolean
     */
    public function getCheckPreOrder()
    {
        return $this->preOrderFlag and !$this->quantity;
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
     * Increment quantity
     *
     * @param $quantity
     * @return $this
     */
    public function incrementQuantity($quantity)
    {
        $this->quantity += $quantity;

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
        $this->history = new \Doctrine\Common\Collections\ArrayCollection();
        $this->shareGroups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return float
     */
    public function getOldPrice()
    {
        return (float)$this->oldPrice;
    }

    /**
     * @param float $oldPrice
     */
    public function setOldPrice($oldPrice)
    {
        $this->oldPrice = $oldPrice;
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
     * Add shareGroup
     *
     * @param ShareSizesGroup $shareGroup
     *
     * @return ProductModelSpecificSize
     */
    public function addShareGroup(ShareSizesGroup $shareGroup)
    {
        if (!$this->inShareGroup($shareGroup)) {
            $this->shareGroups->add($shareGroup);
        }

        return $this;
    }

    /**
     * Remove shareGroup
     *
     * @param ShareSizesGroup $shareGroup
     *
     * @return ProductModelSpecificSize
     */
    public function removeShareGroup(ShareSizesGroup $shareGroup)
    {
        $this->shareGroups->removeElement($shareGroup);

        return $this;
    }

    /**
     * Get shareGroup
     *
     * @return Collection
     */
    public function getShareGroups()
    {
        return $this->shareGroups;
    }

    /**
     * @param ShareSizesGroup $shareGroup
     * @return boolean
     */
    public function inShareGroup(ShareSizesGroup $shareGroup)
    {
        return $this->shareGroups->contains($shareGroup);
    }

    /**
     * @param ShareSizesGroup $shareGroup
     * @return self
     */
    public function toggleShareGroup(ShareSizesGroup $shareGroup)
    {
        if ($this->inShareGroup($shareGroup)) {
            $this->removeShareGroup($shareGroup);
        } else {
            $this->addShareGroup($shareGroup);
        }

        return $this;
    }

    /**
     * @return ShareSizesGroup
     */
    public function getShareGroup()
    {
        return $this->shareGroup;
    }

    /**
     * @param $shareGroup
     *
     * @return $this
     */
    public function setShareGroup($shareGroup)
    {
        $this->shareGroup = $shareGroup;

        return $this;
    }

    /**
     * Return share with highest priority
     *
     * @return Share|null
     */
    public function getShare()
    {
        return $this->shareGroup ? $this->shareGroup->getShare() : null;
    }

    /**
     * Return share with highest priority
     *
     * @return Share[]
     */
    public function getShares()
    {
        $shares = $this->shareGroups->map(function (ShareSizesGroup $shareGroup) {
            return $shareGroup->getShare();
        })->toArray();
        return array_unique($shares);
    }

    public function addHistory($history)
    {
        $this->history[] = $history;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistory()
    {
        return $this->history;
    }


    /**
     * Remove history
     *
     * @param \AppBundle\Entity\ProductModelSpecificSizeHistory $history
     */
    public function removeHistory(\AppBundle\Entity\ProductModelSpecificSizeHistory $history)
    {
        $this->history->removeElement($history);
    }
}
