<?php

namespace AppBundle\Entity;

/**
 * Orders
 */
class Orders
{

    const PAY_TYPE_BANK_CARD = 0;

    const PAY_TYPE_COD = 1;

    const TYPE_NORMAL = 0;

    const TYPE_QUICK = 1;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $status = '0';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $customDelivery;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var string
     */
    private $fio;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $pay;

    /**
     * @var string
     */
    private $totalPrice = '0.00';

    /**
     * @var string
     */
    private $clientSmsId;

    /**
     * @var string
     */
    private $clientSmsStatus;

    /**
     * @var string
     */
    private $managerSmsId;

    /**
     * @var string
     */
    private $managerSmsStatus;

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
    private $cart;

    /**
     * @var \AppBundle\Entity\Users
     */
    private $users;

    /**
     * @var \AppBundle\Entity\Carriers
     */
    private $carriers;

    /**
     * @var \AppBundle\Entity\Cities
     */
    private $cities;

    /**
     * @var \AppBundle\Entity\Stores
     */
    private $stores;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cart = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set status
     *
     * @param integer $status
     *
     * @return Orders
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
     * Set type
     *
     * @param string $type
     *
     * @return Orders
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set customDelivery
     *
     * @param string $customDelivery
     *
     * @return Orders
     */
    public function setCustomDelivery($customDelivery)
    {
        $this->customDelivery = $customDelivery;

        return $this;
    }

    /**
     * Get customDelivery
     *
     * @return string
     */
    public function getCustomDelivery()
    {
        return $this->customDelivery;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Orders
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set fio
     *
     * @param string $fio
     *
     * @return Orders
     */
    public function setFio($fio)
    {
        $this->fio = $fio;

        return $this;
    }

    /**
     * Get fio
     *
     * @return string
     */
    public function getFio()
    {
        return $this->fio;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Orders
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set pay
     *
     * @param string $pay
     *
     * @return Orders
     */
    public function setPay($pay)
    {
        $this->pay = $pay;

        return $this;
    }

    /**
     * Get pay
     *
     * @return string
     */
    public function getPay()
    {
        return $this->pay;
    }

    /**
     * Set totalPrice
     *
     * @param string $totalPrice
     *
     * @return Orders
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice
     *
     * @return string
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * Set clientSmsId
     *
     * @param string $clientSmsId
     *
     * @return Orders
     */
    public function setClientSmsId($clientSmsId)
    {
        $this->clientSmsId = $clientSmsId;

        return $this;
    }

    /**
     * Get clientSmsId
     *
     * @return string
     */
    public function getClientSmsId()
    {
        return $this->clientSmsId;
    }

    /**
     * Set clientSmsStatus
     *
     * @param string $clientSmsStatus
     *
     * @return Orders
     */
    public function setClientSmsStatus($clientSmsStatus)
    {
        $this->clientSmsStatus = $clientSmsStatus;

        return $this;
    }

    /**
     * Get clientSmsStatus
     *
     * @return string
     */
    public function getClientSmsStatus()
    {
        return $this->clientSmsStatus;
    }

    /**
     * Set managerSmsId
     *
     * @param string $managerSmsId
     *
     * @return Orders
     */
    public function setManagerSmsId($managerSmsId)
    {
        $this->managerSmsId = $managerSmsId;

        return $this;
    }

    /**
     * Get managerSmsId
     *
     * @return string
     */
    public function getManagerSmsId()
    {
        return $this->managerSmsId;
    }

    /**
     * Set managerSmsStatus
     *
     * @param string $managerSmsStatus
     *
     * @return Orders
     */
    public function setManagerSmsStatus($managerSmsStatus)
    {
        $this->managerSmsStatus = $managerSmsStatus;

        return $this;
    }

    /**
     * Get managerSmsStatus
     *
     * @return string
     */
    public function getManagerSmsStatus()
    {
        return $this->managerSmsStatus;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return Orders
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
     * @return Orders
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
     * Add cart
     *
     * @param \AppBundle\Entity\Cart $cart
     *
     * @return Orders
     */
    public function addCart(\AppBundle\Entity\Cart $cart)
    {
        $this->cart[] = $cart;

        return $this;
    }

    /**
     * Remove cart
     *
     * @param \AppBundle\Entity\Cart $cart
     */
    public function removeCart(\AppBundle\Entity\Cart $cart)
    {
        $this->cart->removeElement($cart);
    }

    /**
     * Get cart
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Set users
     *
     * @param \AppBundle\Entity\Users $users
     *
     * @return Orders
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
     * Set carriers
     *
     * @param \AppBundle\Entity\Carriers $carriers
     *
     * @return Orders
     */
    public function setCarriers(\AppBundle\Entity\Carriers $carriers = null)
    {
        $this->carriers = $carriers;

        return $this;
    }

    /**
     * Get carriers
     *
     * @return \AppBundle\Entity\Carriers
     */
    public function getCarriers()
    {
        return $this->carriers;
    }

    /**
     * Set cities
     *
     * @param \AppBundle\Entity\Cities $cities
     *
     * @return Orders
     */
    public function setCities(\AppBundle\Entity\Cities $cities = null)
    {
        $this->cities = $cities;

        return $this;
    }

    /**
     * Get cities
     *
     * @return \AppBundle\Entity\Cities
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Set stores
     *
     * @param \AppBundle\Entity\Stores $stores
     *
     * @return Orders
     */
    public function setStores(\AppBundle\Entity\Stores $stores = null)
    {
        $this->stores = $stores;

        return $this;
    }

    /**
     * Get stores
     *
     * @return \AppBundle\Entity\Stores
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * @return string
     */
    public function __toString() {
        return (string)$this->getId();
    }
}
