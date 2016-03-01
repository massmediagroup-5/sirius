<?php

namespace AppBundle\Entity;

/**
 * Filters
 */
class Filters
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
    private $characteristics;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->characteristics = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Filters
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
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return Filters
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
     * @return Filters
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
     * Add characteristic
     *
     * @param \AppBundle\Entity\Characteristics $characteristic
     *
     * @return Filters
     */
    public function addCharacteristic(\AppBundle\Entity\Characteristics $characteristic)
    {
        $this->characteristics[] = $characteristic;

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
}
