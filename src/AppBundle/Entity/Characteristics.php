<?php

namespace AppBundle\Entity;

/**
 * Characteristics
 */
class Characteristics
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
    private $inFilter;

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
    private $characteristicValues;

    /**
     * @var \AppBundle\Entity\CharacteristicGroups
     */
    private $characteristicGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $categories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $filters;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->characteristicValues = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->filters = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * __toString
     *
     */
    public function __toString()
    {
        return (string)$this->getName();
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
     * @return Characteristics
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
     * @return Characteristics
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
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return Characteristics
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
     * @return Characteristics
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
     * Add characteristicValue
     *
     * @param \AppBundle\Entity\CharacteristicValues $characteristicValue
     *
     * @return Characteristics
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
     * Set characteristicGroups
     *
     * @param \AppBundle\Entity\CharacteristicGroups $characteristicGroups
     *
     * @return Characteristics
     */
    public function setCharacteristicGroups(\AppBundle\Entity\CharacteristicGroups $characteristicGroups = null)
    {
        $this->characteristicGroups = $characteristicGroups;

        return $this;
    }

    /**
     * Get characteristicGroups
     *
     * @return \AppBundle\Entity\CharacteristicGroups
     */
    public function getCharacteristicGroups()
    {
        return $this->characteristicGroups;
    }

    /**
     * Add category
     *
     * @param \AppBundle\Entity\Categories $category
     *
     * @return Characteristics
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
     * Add filter
     *
     * @param \AppBundle\Entity\Filters $filter
     *
     * @return Characteristics
     */
    public function addFilter(\AppBundle\Entity\Filters $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Remove filter
     *
     * @param \AppBundle\Entity\Filters $filter
     */
    public function removeFilter(\AppBundle\Entity\Filters $filter)
    {
        $this->filters->removeElement($filter);
    }

    /**
     * Get filters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * inAllFilter
     *
     * lifecycleCallback
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $event
     */
    public function inAllFilter(\Doctrine\ORM\Event\LifecycleEventArgs $event)
    {
        $em = $event->getObjectManager();
        $allFilter = $em->getRepository('AppBundle:Filters')
            ->findOneByName('all');
        if (!$allFilter->getCharacteristics()->contains($this)) {
            $this->addFilter($allFilter);
            $allFilter->addCharacteristic($this);
            $em->persist($allFilter);
            $em->persist($this);
            $em->flush();
        }
        return $this;
    }
}
