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
     * @var string
     */
    private $discountedTotalPrice = 0;

    /**
     * @var string
     */
    private $totalPrice = 0;

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
     * Set discountedTotalPrice
     *
     * @param string $discountedTotalPrice
     *
     * @return OrderProductSize
     */
    public function setDiscountedTotalPrice($discountedTotalPrice)
    {
        $this->discountedTotalPrice = $discountedTotalPrice;

        return $this;
    }

    /**
     * Get discountedTotalPrice
     *
     * @return string
     */
    public function getDiscountedTotalPrice()
    {
        return $this->discountedTotalPrice;
    }

    /**
     * Set totalPrice
     *
     * @param string $totalPrice
     *
     * @return OrderProductSize
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice
     *
     * @return string
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
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
}
