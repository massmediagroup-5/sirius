<?php

namespace AppBundle\Entity;

/**
 * Wishlist
 */
class Wishlist
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $status = '0';

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var \AppBundle\Entity\Users
     */
    private $users;

    /**
     * @var \AppBundle\Entity\ProductModels
     */
    private $productModels;


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
     * Set status
     *
     * @param integer $status
     *
     * @return Wishlist
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
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return Wishlist
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
     * @return Wishlist
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
     * Set users
     *
     * @param \AppBundle\Entity\Users $users
     *
     * @return Wishlist
     */
    public function setUsers(\AppBundle\Entity\Users $users = null)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return \AppBundle\Entity\Users
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set productModels
     *
     * @param \AppBundle\Entity\ProductModels $productModels
     *
     * @return Wishlist
     */
    public function setProductModels(\AppBundle\Entity\ProductModels $productModels = null)
    {
        $this->productModels = $productModels;

        return $this;
    }

    /**
     * Get productModels
     *
     * @return \AppBundle\Entity\ProductModels
     */
    public function getProductModels()
    {
        return $this->productModels;
    }
}
