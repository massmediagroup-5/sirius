<?php

namespace AppBundle\Entity;
use AppBundle\Traits\ProcessHasMany;

/**
 * ProductModels
 */
class ProductModels
{
    use ProcessHasMany;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $price = 0.0;

    /**
     * @var string
     */
    private $wholesalePrice = 0.0;

    /**
     * @var integer
     */
    private $priority = 0;

    /**
     * @var integer
     */
    private $preOrderFlag = 0;

    /**
     * @var integer
     */
    private $status = 0;

    /**
     * @var boolean
     */
    private $active;

    /**
     * @var boolean
     */
    private $inStock;

    /**
     * @var boolean
     */
    private $published;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var \AppBundle\Entity\ProductColors
     */
    private $productColors;

    /**
     * @var \AppBundle\Entity\ProductColors
     */
    private $decorationColor;

    /**
     * @var \AppBundle\Entity\Products
     */
    private $products;

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
     * Set alias
     *
     * @param string $alias
     *
     * @return ProductModels
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return ProductModels
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
     * Get price
     *
     * @return string
     */
    public function getPackagePrice()
    {
        return $this->price * $this->sizes->count();
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return ProductModels
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return ProductModels
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
     * @param integer $preOrderFlag
     * @return ProductModels
     */
    public function setPreOrderFlag($preOrderFlag)
    {
        $this->preOrderFlag = $preOrderFlag;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPreOrderFlag()
    {
        return $this->preOrderFlag;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return ProductModels
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
     * Set inStock
     *
     * @param boolean $inStock
     *
     * @return ProductModels
     */
    public function setInStock($inStock)
    {
        $this->inStock = $inStock;

        return $this;
    }

    /**
     * Get inStock
     *
     * @return boolean
     */
    public function getInStock()
    {
        return $this->inStock;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return ProductModels
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return ProductModels
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
     * @return ProductModels
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
     * Set productColors
     *
     * @param \AppBundle\Entity\ProductColors $productColors
     *
     * @return ProductModels
     */
    public function setProductColors(\AppBundle\Entity\ProductColors $productColors = null)
    {
        $this->productColors = $productColors;

        return $this;
    }

    /**
     * Get productColors
     *
     * @return \AppBundle\Entity\ProductColors
     */
    public function getProductColors()
    {
        return $this->productColors;
    }

    /**
     * Set decorationColor
     *
     * @param \AppBundle\Entity\ProductColors $decorationColors
     *
     * @return ProductModels
     */
    public function setDecorationColor(ProductColors $decorationColors = null)
    {
        $this->decorationColor = $decorationColors;

        return $this;
    }

    /**
     * Get decorationColors
     *
     * @return \AppBundle\Entity\ProductColors
     */
    public function getDecorationColor()
    {
        return $this->decorationColor;
    }

    /**
     * Set products
     *
     * @param \AppBundle\Entity\Products $products
     *
     * @return ProductModels
     */
    public function setProducts(\AppBundle\Entity\Products $products = null)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * Get products
     *
     * @return \AppBundle\Entity\Products
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     *
     */
    public function checkPrice()
    {
        // Add your code here
    }

    /**
     *
     */
    public function checkColor()
    {
        // Add your code here
    }

    /**
     * Set wholesalePrice
     *
     * @param string $wholesalePrice
     *
     * @return ProductModels
     */
    public function setWholesalePrice($wholesalePrice)
    {
        $this->wholesalePrice = $wholesalePrice;

        return $this;
    }

    /**
     * Get wholesalePrice
     *
     * @return int
     */
    public function getWholesalePrice()
    {
        return (float)$this->wholesalePrice;
    }

    /**
     * Add size
     *
     * @param ProductModelSpecificSize $size
     *
     * @return ProductModels
     */
    public function addSize(ProductModelSpecificSize $size)
    {
        $this->sizes[] = $size;

        return $this;
    }

    /**
     * Remove size
     *
     * @param ProductModelSpecificSize $size
     */
    public function removeSize(ProductModelSpecificSize $size)
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

    public function __toString()
    {
        return $this->products ? "{$this->products->getName()} ({$this->productColors->getName()})" : '';
    }
}
