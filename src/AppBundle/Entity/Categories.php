<?php

namespace AppBundle\Entity;
use AppBundle\Traits\ProcessHasMany;

/**
 * Categories
 */
class Categories implements CharacteristicableInterface
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
    private $alias;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var boolean
     */
    private $inMenu;

    /**
     * @var boolean
     */
    private $active;

    /**
     * @var string
     */
    private $content;

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
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var \AppBundle\Entity\Filters
     */
    private $filters;

    /**
     * @var \AppBundle\Entity\Categories
     */
    private $parrent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $characteristicValues;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $characteristics;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $basedProducts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->characteristicValues = new \Doctrine\Common\Collections\ArrayCollection();
        $this->characteristics = new \Doctrine\Common\Collections\ArrayCollection();
        $this->basedProducts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Categories
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
     * Set alias
     *
     * @param string $alias
     *
     * @return Categories
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
     * Set priority
     *
     * @param integer $priority
     *
     * @return Categories
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
     * Set inMenu
     *
     * @param boolean $inMenu
     *
     * @return Categories
     */
    public function setInMenu($inMenu)
    {
        $this->inMenu = $inMenu;

        return $this;
    }

    /**
     * Get inMenu
     *
     * @return boolean
     */
    public function getInMenu()
    {
        return $this->inMenu;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Categories
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
     * Set content
     *
     * @param string $content
     *
     * @return Categories
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
     * Set seoTitle
     *
     * @param string $seoTitle
     *
     * @return Categories
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
     * @return Categories
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
     * @return Categories
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
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return Categories
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
     * @return Categories
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
     * Set filters
     *
     * @param \AppBundle\Entity\Filters $filters
     *
     * @return Categories
     */
    public function setFilters(\AppBundle\Entity\Filters $filters = null)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Get filters
     *
     * @return \AppBundle\Entity\Filters
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Set parrent
     *
     * @param \AppBundle\Entity\Categories $parrent
     *
     * @return Categories
     */
    public function setParrent(\AppBundle\Entity\Categories $parrent = null)
    {
        $this->parrent = $parrent;

        return $this;
    }

    /**
     * Get parrent
     *
     * @return \AppBundle\Entity\Categories
     */
    public function getParrent()
    {
        return $this->parrent;
    }

    /**
     * Add characteristicValue
     *
     * @param \AppBundle\Entity\CharacteristicValues $characteristicValue
     *
     * @return Categories
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
     * Add characteristic
     *
     * @param \AppBundle\Entity\Characteristics $characteristic
     *
     * @return Categories
     */
    public function addCharacteristic(\AppBundle\Entity\Characteristics $characteristic)
    {
        $this->setHasMany('characteristics', [$characteristic], false);

        return $this;
    }

    /**
     * Remove characteristic
     *
     * @param \AppBundle\Entity\Characteristics $characteristic
     */
    public function removeCharacteristic(\AppBundle\Entity\Characteristics $characteristic)
    {
        $this->characteristics->removeElement($characteristic);
    }

    /**
     * Get characteristics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCharacteristics()
    {
        return $this->characteristics;
    }

    /**
     * @return int
     */
    public function getProductsCount()
    {
        return count($this->getBasedProducts());
    }

    /**
     * Add basedProduct
     *
     * @param \AppBundle\Entity\Products $basedProduct
     *
     * @return Categories
     */
    public function addBasedProduct(\AppBundle\Entity\Products $basedProduct)
    {
        $this->basedProducts[] = $basedProduct;

        return $this;
    }

    /**
     * Remove basedProduct
     *
     * @param \AppBundle\Entity\Products $basedProduct
     */
    public function removeBasedProduct(\AppBundle\Entity\Products $basedProduct)
    {
        $this->basedProducts->removeElement($basedProduct);
    }

    /**
     * Get basedProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBasedProducts()
    {
        return $this->basedProducts;
    }

    /**
     *
     */
    public function setAllFilters()
    {
        // Add your code here
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;


    /**
     * Add child
     *
     * @param \AppBundle\Entity\Categories $child
     *
     * @return Categories
     */
    public function addChild(\AppBundle\Entity\Categories $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Categories $child
     */
    public function removeChild(\AppBundle\Entity\Categories $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }
}
