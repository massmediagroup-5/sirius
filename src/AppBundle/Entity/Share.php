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

}