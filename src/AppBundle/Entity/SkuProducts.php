<?php

namespace AppBundle\Entity;

/**
 * SkuProducts
 */
class SkuProducts
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $sku;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $status = '0';

    /**
     * @var boolean
     */
    private $active;

    /**
     * @var string
     */
    private $price = '0.00';

    /**
     * @var integer
     */
    private $quantity = '0';

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var \AppBundle\Entity\ProductModels
     */
    private $productModels;

    /**
     * @var \AppBundle\Entity\Vendors
     */
    private $vendors;


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
     * Set sku
     *
     * @param string $sku
     *
     * @return SkuProducts
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return SkuProducts
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
     * Set status
     *
     * @param integer $status
     *
     * @return SkuProducts
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
     * Set active
     *
     * @param boolean $active
     *
     * @return SkuProducts
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return SkuProducts
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return SkuProducts
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
     * @return SkuProducts
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
     * @return SkuProducts
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
     * Set productModels
     *
     * @param \AppBundle\Entity\ProductModels $productModels
     *
     * @return SkuProducts
     */
    public function setProductModels(\AppBundle\Entity\ProductModels $productModels = null)
    {
        $this->productModels = $productModels;

        return $this;
    }

    /**
     * Get productModels
     *
     * @return \AppBundle\Entity\ProductModels
     */
    public function getProductModels()
    {
        return $this->productModels;
    }

    /**
     * Set vendors
     *
     * @param \AppBundle\Entity\Vendors $vendors
     *
     * @return SkuProducts
     */
    public function setVendors(\AppBundle\Entity\Vendors $vendors = null)
    {
        $this->vendors = $vendors;

        return $this;
    }

    /**
     * Get vendors
     *
     * @return \AppBundle\Entity\Vendors
     */
    public function getVendors()
    {
        return $this->vendors;
    }
}
