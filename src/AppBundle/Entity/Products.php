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
     * @return string
     */
    public function __toString()
    {
        return $this->getImportName() ? : '';
    }
}
