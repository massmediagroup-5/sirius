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
    private $status = 0;

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
     * @var string
     */
    private $originalTotalPrice = 0;

    /**
     * @var string
     */
    private $totalPrice = 0;

    /**
     * @var integer
     */
    private $quantity;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sizes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sizes = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * @param \AppBundle\Entity\CartProductSize $size
     * @return ProductModels
     */
    public function addSize(\AppBundle\Entity\CartProductSize $size)
    {
        $this->sizes[] = $size;

        return $this;
    }

    /**
     * @param \AppBundle\Entity\CartProductSize $site
     */
    public function removeSize(\AppBundle\Entity\CartProductSize $site)
    {
        $this->sizes->removeElement($site);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return array_sum(array_map(function (CartProductSize $size) {
            return $size->getQuantity();
        }, $this->sizes->toArray()));
    }

    /**
     * Set originalTotalPrice
     *
     * @param string $originalTotalPrice
     *
     * @return Cart
     */
    public function setOriginalTotalPrice($originalTotalPrice)
    {
        $this->originalTotalPrice = $originalTotalPrice;

        return $this;
    }

    /**
     * Get originalTotalPrice
     *
     * @return string
     */
    public function getOriginalTotalPrice()
    {
        return $this->originalTotalPrice;
    }

    /**
     * Set totalPrice
     *
     * @param string $totalPrice
     *
     * @return Cart
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
     * @return string
     */
    public function __toString() {
        return $this->getSkuProducts()->getName();
    }

}
