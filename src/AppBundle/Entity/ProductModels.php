<?php

namespace AppBundle\Entity;

use AppBundle\Traits\ProcessHasMany;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * ProductModels
 * @UniqueEntity("alias")
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
     * @var integer
     */
    private $price = 0.0;

    /**
     * @var integer
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
     * @var integer
     */
    private $endCount;

    /**
     * @var boolean
     */
    private $published;

    /**
     * @var boolean
     */
    private $inStock;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var string
     */
    private $textLabel;

    /**
     * @var string
     */
    private $textLabelColor;

    /**
     * @var \AppBundle\Entity\ProductColors
     * @Assert\NotBlank()
     */
    private $productColors;

    /**
     * @var \AppBundle\Entity\ProductColors
     */
    private $decorationColor;

    /**
     * @var \AppBundle\Entity\Products
     * @Assert\NotBlank()
     */
    private $products;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sizes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $images;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $recommended;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $history;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sizes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->recommended = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set endCount
     *
     * @param integer $endCount
     *
     * @return ProductModels
     */
    public function setEndCount($endCount)
    {
        $this->endCount = $endCount;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getEndCount()
    {
        return $this->endCount;
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
     * @return integer
     */
    public function hasPreOrderSize()
    {
        return (bool)$this->sizes->filter(function (ProductModelSpecificSize $size) {
            return $size->getPreOrderFlag();
        });
    }

    /**
     * @return integer
     */
    public function isAllSizesPreOrder()
    {
        return !(bool)$this->sizes->filter(function (ProductModelSpecificSize $size) {
            return !$size->getPreOrderFlag();
        });
    }

    /**
     * @return integer
     */
    public function getAllSizesQuantity()
    {
        return array_sum(array_map(function ($size) {
            return $size->getQuantity();
        }, $this->sizes->toArray()));
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

    /**
     * Add image
     *
     * @param \AppBundle\Entity\ProductModelImage $image
     *
     * @return ProductModels
     */
    public function addImage(\AppBundle\Entity\ProductModelImage $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \AppBundle\Entity\ProductModelImage $image
     */
    public function removeImage(\AppBundle\Entity\ProductModelImage $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }



    /**
     * Add recommended
     *
     * @param \AppBundle\Entity\ProductModels $recommended
     *
     * @return ProductModels
     */
    public function addRecommended(\AppBundle\Entity\ProductModels $recommended)
    {
        $this->recommended[] = $recommended;

        return $this;
    }

    /**
     * Remove recommended
     *
     * @param \AppBundle\Entity\ProductModels $recommended
     */
    public function removeRecommended(\AppBundle\Entity\ProductModels $recommended)
    {
        $this->recommended->removeElement($recommended);
    }

    /**
     * Get recommended
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecommended()
    {
        return $this->recommended;
    }

    /**
     * Get published
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function isPublished()
    {
        return $this->published && $this->products->getActive() && $this->sizes->count();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->products ? "{$this->products->getName()} ({$this->productColors->getName()})" : '';
    }

    /**
     *
     */
    public function __clone()
    {
        $this->id = null;
        $this->images = new ArrayCollection();
        $sizes = new ArrayCollection();
        foreach ($this->sizes as $size) {
            $size = clone $size;
            $size->setModel($this);
            $sizes->add($size);
        }
        $this->sizes = $sizes;
        $this->alias .= '-clone-' . uniqid();
    }

    /**
     * @param ShareSizesGroup $shareGroup
     * @return boolean
     */
    public function inShareGroup(ShareSizesGroup $shareGroup)
    {
        $result = array_unique(array_map(function ($size) use ($shareGroup) {
            return $size->inShareGroup($shareGroup);
        }, $this->sizes->toArray()));

        if(count($result) == 1) {
            return $result[0];
        }
        return false;
    }

    /**
     * @return boolean
     */
    public function isAllSizesHasOneShare()
    {
        // Result will contain grouped sizes ids or null
        $result = array_unique(array_map(function ($size) {
            return $size->getShareGroup() ? $size->getShareGroup()->getId() : null;
        }, $this->sizes->toArray()));

        if(count($result) == 1) {
            return (bool)$result[0];
        }
        return false;
    }

    /**
     * @return ShareSizesGroup|null
     */
    public function getSizesShareGroup()
    {
        if($this->isAllSizesHasOneShare()) {
            return $this->sizes->first()->getShareGroup();
        }
        return null;
    }

    /**
     * Return Share when all sizes has share
     * @return Share|null
     */
    public function getShare()
    {
        if($this->isAllSizesHasOneShare()) {
            return $this->sizes->first()->getShareGroup()->getShare();
        }
        return null;
    }

    /**
     * Set textLabel
     *
     * @param string $textLabel
     *
     * @return ProductModels
     */
    public function setTextLabel($textLabel)
    {
        $this->textLabel = $textLabel;

        return $this;
    }

    /**
     * Get textLabel
     *
     * @return string
     */
    public function getTextLabel()
    {
        return $this->textLabel;
    }

    /**
     * Set textLabelColor
     *
     * @param string $textLabelColor
     *
     * @return ProductModels
     */
    public function setTextLabelColor($textLabelColor)
    {
        $this->textLabelColor = $textLabelColor;

        return $this;
    }

    /**
     * Get textLabelColor
     *
     * @return string
     */
    public function getTextLabelColor()
    {
        return $this->textLabelColor;
    }

    /**
     * Get history
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Add history
     *
     * @param ProductModelsHistory $history
     *
     * @return ProductModels
     */
    public function addHistory(ProductModelsHistory $history)
    {
        $this->history[] = $history;

        return $this;
    }

    public function getQuantityAllSizes(){

        $quantity = 0;
        foreach ($this->getSizes() as $size){
            $quantity += $size->getQuantity();
        }
        return $quantity;
    }

    /**
     * Remove history
     *
     * @param \AppBundle\Entity\ProductModelsHistory $history
     */
    public function removeHistory(\AppBundle\Entity\ProductModelsHistory $history)
    {
        $this->history->removeElement($history);
    }
}
