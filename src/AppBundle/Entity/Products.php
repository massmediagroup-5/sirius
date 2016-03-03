<?php

namespace AppBundle\Entity;

/**
 * Products
 */
class Products
{
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
    private $productImages;

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
    private $categories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $characteristicValues;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productModels = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productImages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add productImage
     *
     * @param \AppBundle\Entity\ProductImages $productImage
     *
     * @return Products
     */
    public function addProductImage(\AppBundle\Entity\ProductImages $productImage)
    {
        $this->productImages[] = $productImage;

        return $this;
    }

    /**
     * Remove productImage
     *
     * @param \AppBundle\Entity\ProductImages $productImage
     */
    public function removeProductImage(\AppBundle\Entity\ProductImages $productImage)
    {
        $this->productImages->removeElement($productImage);
    }

    /**
     * Get productImages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductImages()
    {
        return $this->productImages;
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
     * Add category
     *
     * @param \AppBundle\Entity\Categories $category
     *
     * @return Products
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
     * Add characteristicValue
     *
     * @param \AppBundle\Entity\CharacteristicValues $characteristicValue
     *
     * @return Products
     */
    public function addCharacteristicValue(\AppBundle\Entity\CharacteristicValues $characteristicValue)
    {
        $this->characteristicValues[] = $characteristicValue;

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

    public function __toString()
    {
        return $this->getImportName() ? : '';
    }
}
