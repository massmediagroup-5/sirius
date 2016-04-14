<?php

namespace AppBundle\Entity;
use AppBundle\Traits\ProcessHasMany;

/**
 * Products
 */
class Products implements CharacteristicableInterface
{
    use ProcessHasMany;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $importName;

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
    private $productModels;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $images;

    /**
     * @var \AppBundle\Entity\ActionLabels
     */
    private $actionLabels;

    /**
     * @var \AppBundle\Entity\Categories
     */
    private $baseCategory;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $characteristicValues;

    /**
     * @var string
     */
    private $article;

    /**
     * @var string
     */
    private $name;

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
     * @var string
     */
    private $price = 0;

    /**
     * @var string
     */
    private $wholesalePrice = 0;

    /**
     * @var integer
     */
    private $quantity = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productModels = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->characteristicValues = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set importName
     *
     * @param string $importName
     *
     * @return Products
     */
    public function setImportName($importName)
    {
        $this->importName = $importName;

        return $this;
    }

    /**
     * Get importName
     *
     * @return string
     */
    public function getImportName()
    {
        return $this->importName;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Products
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
     * @return Products
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
     * Set published
     *
     * @param boolean $published
     *
     * @return Products
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
     * @return Products
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
     * @return Products
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
     * Add productModel
     *
     * @param \AppBundle\Entity\ProductModels $productModel
     *
     * @return Products
     */
    public function addProductModel(\AppBundle\Entity\ProductModels $productModel)
    {
        $this->productModels[] = $productModel;

        return $this;
    }

    /**
     * Remove productModel
     *
     * @param \AppBundle\Entity\ProductModels $productModel
     */
    public function removeProductModel(\AppBundle\Entity\ProductModels $productModel)
    {
        $this->productModels->removeElement($productModel);
    }

    /**
     * Get productModels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductModels()
    {
        return $this->productModels;
    }

    /**
     * Add image
     *
     * @param \AppBundle\Entity\ProductImages $image
     *
     * @return Products
     */
    public function addImage(\AppBundle\Entity\ProductImages $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \AppBundle\Entity\ProductImages $image
     */
    public function removeImage(\AppBundle\Entity\ProductImages $image)
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
     * Set actionLabels
     *
     * @param \AppBundle\Entity\ActionLabels $actionLabels
     *
     * @return Products
     */
    public function setActionLabels(\AppBundle\Entity\ActionLabels $actionLabels = null)
    {
        $this->actionLabels = $actionLabels;

        return $this;
    }

    /**
     * Get actionLabels
     *
     * @return \AppBundle\Entity\ActionLabels
     */
    public function getActionLabels()
    {
        return $this->actionLabels;
    }

    /**
     * Set baseCategory
     *
     * @param \AppBundle\Entity\Categories $baseCategory
     *
     * @return Products
     */
    public function setBaseCategory(\AppBundle\Entity\Categories $baseCategory = null)
    {
        $this->baseCategory = $baseCategory;

        return $this;
    }

    /**
     * Get baseCategory
     *
     * @return \AppBundle\Entity\Categories
     */
    public function getBaseCategory()
    {
        return $this->baseCategory;
    }

    /**
     * Add characteristicValue
     *
     * @param \AppBundle\Entity\CharacteristicValues $characteristicValue
     *
     * @return Products
     */
    public function addCharacteristicValue(\AppBundle\Entity\CharacteristicValues $characteristicValue)
    {
        $this->setHasMany('characteristicValues', [$characteristicValue], false);

        return $this;
    }

    /**
     * Remove characteristicValue
     *
     * @param \AppBundle\Entity\CharacteristicValues $characteristicValue
     */
    public function removeCharacteristicValue(\AppBundle\Entity\CharacteristicValues $characteristicValue)
    {
        $this->characteristicValues->removeElement($characteristicValue);
    }

    /**
     * Get characteristicValues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCharacteristicValues()
    {
        return $this->characteristicValues;
    }

    /**
     * PrePersist
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $event
     */
    public function setFirstBaseCategory(\Doctrine\ORM\Event\LifecycleEventArgs $event)
    {
        $em = $event->getObjectManager();
        if (empty($this->getBaseCategory())) {
            if ($category = $em->getRepository('AppBundle:Categories')->find(1)) {
                $this->setBaseCategory($category);
            }
        }
    }

    /**
     * @param $type
     * @return CharacteristicValues|bool
     */
    public function getCharacteristicByType($type)
    {
        return $this->characteristicValues->filter(function (CharacteristicValues $characteristicValue) use($type) {
            return $characteristicValue->getCharacteristics()->getRenderType() == $type;
        })->first();
    }

    /**
     * @param $type
     * @return string
     */
    public function getCharacteristicValueByType($type)
    {
        $characteristicValue = $this->getCharacteristicByType($type);
        return $characteristicValue ? $characteristicValue->getName() : '';
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Products
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
     * Set seoTitle
     *
     * @param string $seoTitle
     *
     * @return Products
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
     * @return Products
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
     * @return Products
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
     * @return Products
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
     * @return Products
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
     * @return Products
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
     * @return string
     */
    public function __toString()
    {
        return $this->getImportName() ? : '';
    }

    /**
     * Set article
     *
     * @param string $article
     *
     * @return Products
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return string
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Products
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
     * @return Products
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Products
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
}
