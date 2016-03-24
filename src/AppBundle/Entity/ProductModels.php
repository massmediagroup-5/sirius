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
    private $name;

    /**
     * @var string
     */
    private $description;

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
    private $oldprice;

    /**
     * @var string
     */
    private $seoTitle;

    /**
     * @var string
     */
    private $seoDescription;

    /**
     * @var string
     */
    private $seoKeywords;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $characteristics;

    /**
     * @var string
     */
    private $features;

    /**
     * @var integer
     */
    private $priority;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $productModelImages;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $skuProducts;

    /**
     * @var \AppBundle\Entity\ProductColors
     */
    private $productColors;

    /**
     * @var \AppBundle\Entity\ProductColors
     */
    private $decorationColor;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sizes;

    /**
     * @var \AppBundle\Entity\Products
     */
    private $products;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productModelImages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->skuProducts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return ProductModels
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
     * Set description
     *
     * @param string $description
     *
     * @return ProductModels
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * Set oldprice
     *
     * @param string $oldprice
     *
     * @return ProductModels
     */
    public function setOldprice($oldprice)
    {
        $this->oldprice = $oldprice;

        return $this;
    }

    /**
     * Get oldprice
     *
     * @return string
     */
    public function getOldprice()
    {
        return $this->oldprice;
    }

    /**
     * Set seoTitle
     *
     * @param string $seoTitle
     *
     * @return ProductModels
     */
    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;

        return $this;
    }

    /**
     * Get seoTitle
     *
     * @return string
     */
    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    /**
     * Set seoDescription
     *
     * @param string $seoDescription
     *
     * @return ProductModels
     */
    public function setSeoDescription($seoDescription)
    {
        $this->seoDescription = $seoDescription;

        return $this;
    }

    /**
     * Get seoDescription
     *
     * @return string
     */
    public function getSeoDescription()
    {
        return $this->seoDescription;
    }

    /**
     * Set seoKeywords
     *
     * @param string $seoKeywords
     *
     * @return ProductModels
     */
    public function setSeoKeywords($seoKeywords)
    {
        $this->seoKeywords = $seoKeywords;

        return $this;
    }

    /**
     * Get seoKeywords
     *
     * @return string
     */
    public function getSeoKeywords()
    {
        return $this->seoKeywords;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return ProductModels
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set characteristics
     *
     * @param string $characteristics
     *
     * @return ProductModels
     */
    public function setCharacteristics($characteristics)
    {
        $this->characteristics = $characteristics;

        return $this;
    }

    /**
     * Get characteristics
     *
     * @return string
     */
    public function getCharacteristics()
    {
        return $this->characteristics;
    }

    /**
     * Set features
     *
     * @param string $features
     *
     * @return ProductModels
     */
    public function setFeatures($features)
    {
        $this->features = $features;

        return $this;
    }

    /**
     * Get features
     *
     * @return string
     */
    public function getFeatures()
    {
        return $this->features;
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
     * Add productModelImage
     *
     * @param \AppBundle\Entity\ProductModelImages $productModelImage
     *
     * @return ProductModels
     */
    public function addProductModelImage(\AppBundle\Entity\ProductModelImages $productModelImage)
    {
        $this->productModelImages[] = $productModelImage;

        return $this;
    }

    /**
     * Remove productModelImage
     *
     * @param \AppBundle\Entity\ProductModelImages $productModelImage
     */
    public function removeProductModelImage(\AppBundle\Entity\ProductModelImages $productModelImage)
    {
        $this->productModelImages->removeElement($productModelImage);
    }

    /**
     * Get productModelImages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductModelImages()
    {
        return $this->productModelImages;
    }

    /**
     * Add skuProduct
     *
     * @param \AppBundle\Entity\SkuProducts $skuProduct
     *
     * @return ProductModels
     */
    public function addSkuProduct(\AppBundle\Entity\SkuProducts $skuProduct)
    {
        $this->skuProducts[] = $skuProduct;

        return $this;
    }

    /**
     * Remove skuProduct
     *
     * @param \AppBundle\Entity\SkuProducts $skuProduct
     */
    public function removeSkuProduct(\AppBundle\Entity\SkuProducts $skuProduct)
    {
        $this->skuProducts->removeElement($skuProduct);
    }

    /**
     * Get skuProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSkuProducts()
    {
        return $this->skuProducts;
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
     * Set sizes
     *
     * @param array $sizes
     *
     * @return ProductModels
     */
    public function setSizes($sizes)
    {
        $this->setHasMany('sizes', $sizes);

        return $this;
    }

    /**
     * Get sizes
     *
     * @return \AppBundle\Entity\ProductModelSizes
     */
    public function getSizes()
    {
        return $this->sizes;
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
     * @var string
     */
    private $wholesalePrice = 0.0;

    /**
     * @var string
     */
    private $wholesaleOldprice = 0.0;


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
     * @return string
     */
    public function getWholesalePrice()
    {
        return $this->wholesalePrice;
    }

    /**
     * Set wholesaleOldprice
     *
     * @param string $wholesaleOldprice
     *
     * @return ProductModels
     */
    public function setWholesaleOldprice($wholesaleOldprice)
    {
        $this->wholesaleOldprice = $wholesaleOldprice;

        return $this;
    }

    /**
     * Get wholesaleOldprice
     *
     * @return string
     */
    public function getWholesaleOldprice()
    {
        return $this->wholesaleOldprice;
    }

    public function __toString()
    {
        return $this->getName() ? "{$this->getName()} ({$this->getProductColors()->getName()})" : '';
    }
}
