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

    const STATUS_WAIT = 0;

    const STATUS_ACCEPTED = 0;

    const STATUS_REJECTED = 0;

    /**
     * @var integer
     */
    private $id;

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
    private $comment_admin;

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
    private $totalPrice = 0;

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
     * @var OrderStatus
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sizes;

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
     * @var string
     */
    private $email;

    /**
     * @var float
     */
    private $discountedTotalPrice = 0;

    /**
     * @var float
     */
    private $individualDiscount = 0;

    /**
     * @var boolean
     */
    private $quickFlag = false;

    /**
     * @var string
     */
    private $additionalSolarDescription;

    /**
     * @var float
     */
    private $additionalSolar = 0;

    /**
     * @var boolean
     */
    private $preOrderFlag = false;

    /**
     * @var \AppBundle\Entity\Orders
     */
    private $relatedOrder;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sizes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param OrderStatus $status
     *
     * @return Orders
     */
    public function setStatus(OrderStatus $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return OrderStatus
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
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return array_sum(array_map(function (OrderProductSize $cartItem) {
            return $cartItem->getQuantity();
        }, $this->sizes->toArray()));
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
     * Set email
     *
     * @param string $email
     *
     * @return Orders
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set discountedTotalPrice
     *
     * @param string $discountedTotalPrice
     *
     * @return Orders
     */
    public function setDiscountedTotalPrice($discountedTotalPrice)
    {
        $this->discountedTotalPrice = $discountedTotalPrice;

        return $this;
    }

    /**
     * Get discountedTotalPrice
     *
     * @return string
     */
    public function getDiscountedTotalPrice()
    {
        return $this->discountedTotalPrice;
    }

    /**
     * Get discount
     *
     * @return string
     */
    public function getDiscount()
    {
        return $this->totalPrice - $this->discountedTotalPrice;
    }

    /**
     * Set individualDiscount
     *
     * @param string $individualDiscount
     *
     * @return Orders
     */
    public function setIndividualDiscount($individualDiscount)
    {
        $this->individualDiscount = $individualDiscount;

        return $this;
    }

    /**
     * Get individualDiscount
     *
     * @return float
     */
    public function getIndividualDiscount()
    {
        return (float)$this->individualDiscount;
    }

    /**
     * Get individual discounted total price
     *
     * @return int
     */
    public function getIndividualDiscountedTotalPrice()
    {
        return $this->getDiscountedTotalPrice() - $this->individualDiscount + $this->additionalSolar;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * Add size
     *
     * @param \AppBundle\Entity\OrderProductSize $size
     *
     * @return Orders
     */
    public function addSize(\AppBundle\Entity\OrderProductSize $size)
    {
        $this->sizes[] = $size;

        return $this;
    }

    /**
     * Remove size
     *
     * @param \AppBundle\Entity\OrderProductSize $size
     */
    public function removeSize(\AppBundle\Entity\OrderProductSize $size)
    {
        $this->sizes->removeElement($size);
    }

    /**
     * Get sizes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * Set quickFlag
     *
     * @param boolean $quickFlag
     *
     * @return Orders
     */
    public function setQuickFlag($quickFlag)
    {
        $this->quickFlag = $quickFlag;

        return $this;
    }

    /**
     * Get quickFlag
     *
     * @return boolean
     */
    public function getQuickFlag()
    {
        return $this->quickFlag;
    }

    /**
     * Set commentAdmin
     *
     * @param string $commentAdmin
     *
     * @return Orders
     */
    public function setCommentAdmin($commentAdmin)
    {
        $this->comment_admin = $commentAdmin;

        return $this;
    }

    /**
     * Get commentAdmin
     *
     * @return string
     */
    public function getCommentAdmin()
    {
        return $this->comment_admin;
    }

    /**
     * Set additionalSolarDescription
     *
     * @param string $additionalSolarDescription
     *
     * @return Orders
     */
    public function setAdditionalSolarDescription($additionalSolarDescription)
    {
        $this->additionalSolarDescription = $additionalSolarDescription;

        return $this;
    }

    /**
     * Get additionalSolarDescription
     *
     * @return string
     */
    public function getAdditionalSolarDescription()
    {
        return $this->additionalSolarDescription;
    }

    /**
     * Set additionalSolar
     *
     * @param string $additionalSolar
     *
     * @return Orders
     */
    public function setAdditionalSolar($additionalSolar)
    {
        $this->additionalSolar = $additionalSolar;

        return $this;
    }

    /**
     * Get additionalSolar
     *
     * @return string
     */
    public function getAdditionalSolar()
    {
        return $this->additionalSolar;
    }

    /**
     * Set preOrderFlag
     *
     * @param boolean $preOrderFlag
     *
     * @return Orders
     */
    public function setPreOrderFlag($preOrderFlag)
    {
        $this->preOrderFlag = $preOrderFlag;

        return $this;
    }

    /**
     * Get preOrderFlag
     *
     * @return boolean
     */
    public function getPreOrderFlag()
    {
        return $this->preOrderFlag;
    }

    /**
     * Set relatedOrder
     *
     * @param \AppBundle\Entity\Orders $relatedOrder
     *
     * @return Orders
     */
    public function setRelatedOrder(\AppBundle\Entity\Orders $relatedOrder = null)
    {
        $this->relatedOrder = $relatedOrder;

        return $this;
    }

    /**
     * Get relatedOrder
     *
     * @return \AppBundle\Entity\Orders
     */
    public function getRelatedOrder()
    {
        return $this->relatedOrder;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        if($this->preOrderFlag) {
            return $this->relatedOrder ? $this->relatedOrder->id : $this->id;
        }
        return $this->id;
    }
}
