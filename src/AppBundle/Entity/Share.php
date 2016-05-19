<?php

namespace AppBundle\Entity;

/**
 * Share
 */
class Share
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
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var integer
     */
    private $priority = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sizesGroups;

    /**
     * @var boolean
     */
    private $status = 0;

    /**
     * @var \DateTime
     */
    private $startTime;

    /**
     * @var \DateTime
     */
    private $endTime;

    /**
     * @var string
     */
    private $class_name;

    /**
     * @var string
     */
    private $image;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sizesGroups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Share
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
     * @return Share
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
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return Share
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
     * Set priority
     *
     * @param integer $priority
     *
     * @return Share
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
     * Add sizesGroup
     *
     * @param \AppBundle\Entity\ShareSizesGroup $sizesGroup
     *
     * @return Share
     */
    public function addSizesGroup(\AppBundle\Entity\ShareSizesGroup $sizesGroup)
    {
        $this->sizesGroups[] = $sizesGroup;

        return $this;
    }

    /**
     * Remove sizesGroup
     *
     * @param \AppBundle\Entity\ShareSizesGroup $sizesGroup
     */
    public function removeSizesGroup(\AppBundle\Entity\ShareSizesGroup $sizesGroup)
    {
        $this->sizesGroups->removeElement($sizesGroup);
    }

    /**
     * Get sizesGroups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSizesGroups()
    {
        return $this->sizesGroups;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Share
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return Share
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     *
     * @return Share
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Check active
     *
     * @return \DateTime
     */
    public function isActive()
    {
        $now = new \DateTime();

        return $this->status && $this->startTime < $now && $this->endTime > $now;
    }

    /**
     * Set className
     *
     * @param string $className
     *
     * @return Share
     */
    public function setClassName($className)
    {
        $this->class_name = $className;

        return $this;
    }

    /**
     * Get className
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Share
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }
}
