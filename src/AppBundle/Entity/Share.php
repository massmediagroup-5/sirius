<?php

namespace AppBundle\Entity;

use AppBundle\Helper\Arr;
use Symfony\Component\Validator\Constraints\NotBlank;

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
     * @NotBlank()
     */
    private $name;

    /**
     * @var string
     * @NotBlank()
     */
    private $description;

    /**
     * @var string
     * @NotBlank()
     */
    private $terms;

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
    private $status = false;

    /**
     * @var boolean
     */
    private $forbidDeactivation = false;

    /**
     * @var boolean
     */
    private $groupsCount = false;

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
     * Discount when products from all groups are in cart
     *
     * @var string
     */
    private $discount = 0;


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
     * Set terms
     *
     * @param string $terms
     *
     * @return Share
     */
    public function setTerms($terms)
    {
        $this->terms = $terms;

        return $this;
    }

    /**
     * Get terms
     *
     * @return string
     */
    public function getTerms()
    {
        return $this->terms;
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
     * @return bool
     */
    public function getForbidDeactivation()
    {
        return $this->forbidDeactivation;
    }

    /**
     * @return bool
     */
    public function isForbidDeactivation()
    {
        return $this->forbidDeactivation;
    }

    /**
     * @param $forbidDeactivation
     *
     * @return $this
     */
    public function setForbidDeactivation($forbidDeactivation)
    {
        $this->forbidDeactivation = $forbidDeactivation;

        return $this;
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

    /**
     * getUploadRootDir
     * the absolute directory path where uploaded
     * documents should be saved
     *
     * @return string
     */
    public function getUploadRootDir()
    {
//        dump(realpath(__DIR__.'/../../../web/'.$this->getUploadDir()));exit;
        // absolute path to your directory where images must be saved
        $path=__DIR__.'/../../../web/'.$this->getUploadDir();
        $path = str_replace('\\','/',$path);
        return $path;
    }

    /**
     * getUploadDir
     * get rid of the __DIR__ so it doesn't screw up
     * when displaying uploaded /img/slider in the view.
     *
     * @return string
     * /img/products
     */
    public function getUploadDir()
    {
        return 'img/shares';
    }

    public function getAbsolutePath()
    {
        return null === $this->getImage() ? null : $this->getUploadRootDir().'/'.$this->getImage();
    }

    public function getWebPath()
    {
        return null === $this->getImage() ? null : '/'.$this->getUploadDir().'/'.$this->getImage();
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set discount
     *
     * @param string $discount
     *
     * @return Share
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set groupsCount
     *
     * @param boolean $groupsCount
     *
     * @return Share
     */
    public function setGroupsCount($groupsCount)
    {
        $this->groupsCount = $groupsCount;

        return $this;
    }

    /**
     * Get groupsCount
     *
     * @return boolean
     */
    public function getGroupsCount()
    {
        return $this->groupsCount;
    }

    /**
     * Get groupsCount
     *
     * @return ShareSizesGroupDiscount[]
     */
    public function getGroupsDiscounts()
    {
        $discounts = [];
        /** @var ShareSizesGroup $group */
        foreach ($this->sizesGroups as $group) {
            $discounts = array_merge($discounts, $group->getDiscounts()->toArray());
        }
        return $discounts;
    }

    /**
     * @return array
     */
    public function getActualGroupsDiscounts()
    {
        return Arr::where($this->getGroupsDiscounts(), function ($key, ShareSizesGroupDiscount $discount) {
            return $discount->getDiscount();
        });
    }

    /**
     * @return bool
     */
    public function hasGlobalGroupDiscount()
    {
        foreach ($this->sizesGroups as $sizesGroup) {
            if ($sizesGroup->getDiscount()) {
                return true;
            }
        }

        return false;
    }
}
