<?php

namespace AppBundle\Entity;

/**
 * OrderProductSize
 */
class OrderProductSize
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $status = '0';

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var integer
     */
    private $quantity;

    /**
     * @var \AppBundle\Entity\ProductModelSpecificSize
     */
    private $size;

    /**
     * @var \AppBundle\Entity\Orders
     */
    private $order;

    /**
     * @var string
     */
    private $discountedTotalPricePerItem = 0;

    /**
     * @var string
     */
    private $totalPricePerItem = 0;


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
     * Set status
     *
     * @param integer $status
     *
     * @return OrderProductSize
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get discountedTotalPrice
     *
     * @return string
     */
    public function getDiscountedTotalPrice()
    {
        return $this->discountedTotalPricePerItem * $this->quantity;
    }

    /**
     * Get totalPrice
     *
     * @return string
     */
    public function getTotalPrice()
    {
        return $this->totalPricePerItem * $this->quantity;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return OrderProductSize
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set updateTime
     *
     * @param \DateTime $updateTime
     *
     * @return OrderProductSize
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;

        return $this;
    }

    /**
     * Get updateTime
     *
     * @return \DateTime
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return OrderProductSize
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Increment quantity
     *
     * @param integer $quantity
     *
     * @return OrderProductSize
     */
    public function incrementQuantity($quantity)
    {
        $this->quantity += $quantity;

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
     * Set size
     *
     * @param \AppBundle\Entity\ProductModelSpecificSize $size
     *
     * @return OrderProductSize
     */
    public function setSize(\AppBundle\Entity\ProductModelSpecificSize $size = null)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return \AppBundle\Entity\ProductModelSpecificSize
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set order
     *
     * @param \AppBundle\Entity\Orders $order
     *
     * @return OrderProductSize
     */
    public function setOrder(\AppBundle\Entity\Orders $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \AppBundle\Entity\Orders
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set discountedTotalPricePerItem
     *
     * @param string $discountedTotalPricePerItem
     *
     * @return OrderProductSize
     */
    public function setDiscountedTotalPricePerItem($discountedTotalPricePerItem)
    {
        $this->discountedTotalPricePerItem = $discountedTotalPricePerItem;

        return $this;
    }

    /**
     * Get discountedTotalPricePerItem
     *
     * @return string
     */
    public function getDiscountedTotalPricePerItem()
    {
        return $this->discountedTotalPricePerItem;
    }

    /**
     * Get discountedTotalPricePerItem
     *
     * @return string
     */
    public function getDiscountPercentages()
    {
        return ($this->totalPricePerItem - $this->discountedTotalPricePerItem) / $this->totalPricePerItem * 100;
    }

    /**
     * Set totalPricePerItem
     *
     * @param string $totalPricePerItem
     *
     * @return OrderProductSize
     */
    public function setTotalPricePerItem($totalPricePerItem)
    {
        $this->totalPricePerItem = $totalPricePerItem;

        return $this;
    }

    /**
     * Get totalPricePerItem
     *
     * @return string
     */
    public function getTotalPricePerItem()
    {
        return $this->totalPricePerItem;
    }

    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @return bool
     */
    public function hasValidQuantity()
    {
        return !($this->getQuantity() > $this->size->getQuantity() && !$this->size->getPreOrderFlag());
    }
}
