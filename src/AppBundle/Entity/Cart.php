<?php

namespace AppBundle\Entity;

/**
 * Cart
 */
class Cart
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
     * @var integer
     */
    private $quantity = '1';

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var \AppBundle\Entity\Users
     */
    private $users;

    /**
     * @var \AppBundle\Entity\Orders
     */
    private $orders;

    /**
     * @var \AppBundle\Entity\SkuProducts
     */
    private $skuProducts;


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
     * @return Cart
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Cart
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
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return Cart
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
     * @return Cart
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
     * Set users
     *
     * @param \AppBundle\Entity\Users $users
     *
     * @return Cart
     */
    public function setUsers(\AppBundle\Entity\Users $users = null)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return \AppBundle\Entity\Users
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set orders
     *
     * @param \AppBundle\Entity\Orders $orders
     *
     * @return Cart
     */
    public function setOrders(\AppBundle\Entity\Orders $orders = null)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * Get orders
     *
     * @return \AppBundle\Entity\Orders
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Set skuProducts
     *
     * @param \AppBundle\Entity\SkuProducts $skuProducts
     *
     * @return Cart
     */
    public function setSkuProducts(\AppBundle\Entity\SkuProducts $skuProducts = null)
    {
        $this->skuProducts = $skuProducts;

        return $this;
    }

    /**
     * Get skuProducts
     *
     * @return \AppBundle\Entity\SkuProducts
     */
    public function getSkuProducts()
    {
        return $this->skuProducts;
    }
}
