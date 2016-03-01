<?php

namespace AppBundle\Entity;

/**
 * CharacteristicValues
 */
class CharacteristicValues
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var boolean
     */
    private $inFilter = false;

    /**
     * @var boolean
     */
    private $notParse = false;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var \AppBundle\Entity\Characteristics
     */
    private $characteristics;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $categories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $products;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * __toString
     *
     */
    public function __toString()
    {
        return $this->characteristics.': '.$this->name;
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
     * @return CharacteristicValues
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
     * Set inFilter
     *
     * @param boolean $inFilter
     *
     * @return CharacteristicValues
     */
    public function setInFilter($inFilter)
    {
        $this->inFilter = $inFilter;

        return $this;
    }

    /**
     * Get inFilter
     *
     * @return boolean
     */
    public function getInFilter()
    {
        return $this->inFilter;
    }

    /**
     * Set notParse
     *
     * @param boolean $notParse
     *
     * @return CharacteristicValues
     */
    public function setNotParse($notParse)
    {
        $this->notParse = $notParse;

        return $this;
    }

    /**
     * Get notParse
     *
     * @return boolean
     */
    public function getNotParse()
    {
        return $this->notParse;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return CharacteristicValues
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
     * @return CharacteristicValues
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
     * Set characteristics
     *
     * @param \AppBundle\Entity\Characteristics $characteristics
     *
     * @return CharacteristicValues
     */
    public function setCharacteristics(\AppBundle\Entity\Characteristics $characteristics = null)
    {
        $this->characteristics = $characteristics;

        return $this;
    }

    /**
     * Get characteristics
     *
     * @return \AppBundle\Entity\Characteristics
     */
    public function getCharacteristics()
    {
        return $this->characteristics;
    }

    /**
     * Add category
     *
     * @param \AppBundle\Entity\Categories $category
     *
     * @return CharacteristicValues
     */
    public function addCategory(\AppBundle\Entity\Categories $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \AppBundle\Entity\Categories $category
     */
    public function removeCategory(\AppBundle\Entity\Categories $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add product
     *
     * @param \AppBundle\Entity\Products $product
     *
     * @return CharacteristicValues
     */
    public function addProduct(\AppBundle\Entity\Products $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \AppBundle\Entity\Products $product
     */
    public function removeProduct(\AppBundle\Entity\Products $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
