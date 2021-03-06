<?php

namespace AppBundle\Entity;

use AppBundle\Helper\Arr;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * Orders
 */
class Orders
{

    const PAY_TYPE_BANK_CARD = 0;

    const PAY_TYPE_COD = 1;

    const PAY_TYPE_EMPTY = 2;

    const TYPE_NORMAL = 0;

    const TYPE_QUICK = 1;

    const STATUS_WAIT = 0;

    const STATUS_ACCEPTED = 0;

    const STATUS_REJECTED = 0;

    const STATUS_DONE = 'done';

    /**
     * @var integer
     */
    private $id;

    /**
     * @var int
     */
    private $type = 0;

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
    private $ttn;

    /**
     * @var string
     */
    private $customTtn;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var int
     */
    private $pay;

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
     * @var \DateTime
     */
    private $doneTime;

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
    private $individualDiscountedTotalPrice = 0;

    /**
     * @var float
     */
    private $individualDiscount = 0;

    /**
     * @var float
     */
    private $loyalityDiscount = 0;

    /**
     * @var float
     */
    private $upSellDiscount = 0;

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
     * @var \AppBundle\Entity\OrderStatusPay
     */
    private $payStatus;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $history;

    /**
     * @var integer
     */
    private $bonuses = 0;

    /**
     * Bonuses wrote off from user
     *
     * @var integer
     */
    private $bonusesWroteOff = 0;

    /**
     * @var boolean
     */
    private $bonusesEnrolled = false;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $smsInfo;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sizes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->smsInfo = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get fio
     *
     * @return string
     */
    public function getName()
    {
        return explode(' ', $this->fio)[0];
    }

    /**
     * Get fio
     *
     * @return string
     */
    public function getSurname()
    {
        return last(explode(' ', $this->fio));
    }

    /**
     * Set ttn
     *
     * @param string $ttn
     *
     * @return Orders
     */
    public function setTtn($ttn)
    {
        $this->ttn = $ttn;

        return $this;
    }

    /**
     * Get ttn
     *
     * @return string
     */
    public function getTtn()
    {
        return $this->ttn;
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
     * @param int $pay
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
     * @return int
     */
    public function getPay()
    {
        return $this->pay;
    }

    /**
     * Get totalPrice
     *
     * @return string
     */
    public function getTotalPrice()
    {
        return array_sum($this->sizes->map(function (OrderProductSize $size) {
            return $size->getTotalPrice();
        })->toArray());
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
     * @return float
     */
    public function getDiscountedTotalPrice()
    {
        return array_sum($this->sizes->map(function (OrderProductSize $size) {
            return $size->getDiscountedTotalPrice();
        })->toArray());
    }

    /**
     * Get discount
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->getTotalPrice() - $this->getDiscountedTotalPrice();
    }

    /**
     * Get discount
     *
     * @return string
     */
    public function getFullDiscount()
    {
        return $this->getTotalPrice() - $this->getIndividualDiscountedTotalPrice();
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
     * @return float
     */
    public function getLoyaltyAndUpSellDiscounted()
    {
        return $this->getDiscountedTotalPrice() - $this->loyalityDiscount - $this->upSellDiscount;
    }

    /**
     * Get individual discounted total price
     *
     * @return int
     */
    public function getIndividualDiscountedTotalPrice()
    {
        return (float)$this->individualDiscountedTotalPrice;
    }

    /**
     * Calculate and set individual discounted total price
     *
     * @return $this
     */
    public function setIndividualDiscountedTotalPrice()
    {
        $this->individualDiscountedTotalPrice = $this->getDiscountedTotalPrice() - $this->getAdditionalDiscounts();

        return $this;
    }

    /**
     * Get additional discounts. E. g. discounts not related to specific size.
     *
     * @return int
     */
    public function getAdditionalDiscounts()
    {
        return $this->individualDiscount - $this->additionalSolar + $this->bonuses + $this->loyalityDiscount
            + $this->upSellDiscount;
    }

    public function getStringForFilter()
    {
        return '№ заказа: ' . $this->getIdentifier() . ', ' . ( $this->fio ? 'Обычный, '.$this->fio : 'Быстрый заказ' ) . ', ' . $this->phone . ', ' . $this->createTime->format('d-m-Y H:i:s');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getIdentifier();
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
     * Set sizes
     *
     * @param \Doctrine\Common\Collections\Collection $sizes
     *
     * @return Orders
     */
    public function setSizes(\Doctrine\Common\Collections\Collection $sizes)
    {
        $this->sizes = $sizes;

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
     * @return float
     */
    public function getAdditionalSolar()
    {
        return (float)$this->additionalSolar;
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
     * @return string
     */
    public function getIdentifier()
    {
        if ($this->preOrderFlag) {
            return $this->getMainId() . '/п';
        }

        return $this->getMainId();
    }

    /**
     * Get id of first order
     *
     * @return string
     */
    public function getMainId()
    {
        return $this->relatedOrder ? min($this->relatedOrder->id, $this->id) : $this->id;
    }

    /**
     * @return Orders
     */
    public function getMain()
    {
        if ($this->relatedOrder) {
            return $this->relatedOrder->id > $this->id ? $this : $this->relatedOrder;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function isMain()
    {
        return $this->relatedOrder ? $this->id < $this->relatedOrder->id : true;
    }

    /**
     * @return string
     */
    public function getMaxBonuses()
    {
        if ($this->relatedOrder) {
            return $this->getMain()->getBonusesWroteOff() - $this->relatedOrder->getBonuses();
        }
        return $this->bonusesWroteOff;
    }

    public function __clone()
    {
        $this->id = null;
        $this->bonuses = 0;
        $this->bonusesWroteOff = 0;
        $this->sizes = new ArrayCollection();
    }

    /**
     * Set payStatus
     *
     * @param \AppBundle\Entity\OrderStatusPay $payStatus
     *
     * @return Orders
     */
    public function setPayStatus(\AppBundle\Entity\OrderStatusPay $payStatus = null)
    {
        $this->payStatus = $payStatus;

        return $this;
    }

    /**
     * Get payStatus
     *
     * @return \AppBundle\Entity\OrderStatusPay
     */
    public function getPayStatus()
    {
        return $this->payStatus;
    }

    /**
     * Add history
     *
     * @param OrderHistory $history
     *
     * @return Orders
     */
    public function addHistory(OrderHistory $history)
    {
        $this->history[] = $history;

        return $this;
    }

    /**
     * Remove history
     *
     * @param OrderHistory $history
     */
    public function removeHistory(OrderHistory $history)
    {
        $this->history->removeElement($history);
    }

    /**
     * Get history
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Set bonuses
     *
     * @param integer $bonuses
     *
     * @return Orders
     */
    public function setBonuses($bonuses)
    {
        $this->bonuses = (int)$bonuses;

        return $this;
    }

    /**
     * Get bonuses
     *
     * @return integer
     */
    public function getBonuses()
    {
        return $this->bonuses;
    }

    /**
     * Set bonusesEnrolled
     *
     * @param boolean $bonusesEnrolled
     *
     * @return Orders
     */
    public function setBonusesEnrolled($bonusesEnrolled)
    {
        $this->bonusesEnrolled = $bonusesEnrolled;

        return $this;
    }

    /**
     * Get bonusesEnrolled
     *
     * @return boolean
     */
    public function getBonusesEnrolled()
    {
        return $this->bonusesEnrolled;
    }

    /**
     * Set doneTime
     *
     * @param \DateTime $doneTime
     *
     * @return Orders
     */
    public function setDoneTime($doneTime)
    {
        $this->doneTime = $doneTime;

        return $this;
    }

    /**
     * Get doneTime
     *
     * @return \DateTime
     */
    public function getDoneTime()
    {
        return $this->doneTime;
    }

    /**
     * @return float
     */
    public function getLoyalityDiscount()
    {
        return (float)$this->loyalityDiscount;
    }

    /**
     * @param float $loyalityDiscount
     */
    public function setLoyalityDiscount($loyalityDiscount)
    {
        $this->loyalityDiscount = $loyalityDiscount;
    }


    /**
     * Add smsInfo
     *
     * @param \AppBundle\Entity\OrderSmsInfo $smsInfo
     *
     * @return Orders
     */
    public function addSmsInfo(\AppBundle\Entity\OrderSmsInfo $smsInfo)
    {
        $this->smsInfo[] = $smsInfo;

        return $this;
    }

    /**
     * Remove smsInfo
     *
     * @param \AppBundle\Entity\OrderSmsInfo $smsInfo
     */
    public function removeSmsInfo(\AppBundle\Entity\OrderSmsInfo $smsInfo)
    {
        $this->smsInfo->removeElement($smsInfo);
    }

    /**
     * Get smsInfo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSmsInfo()
    {
        return $this->smsInfo;
    }

    /**
     * Set upSellDiscount
     *
     * @param string $upSellDiscount
     *
     * @return Orders
     */
    public function setUpSellDiscount($upSellDiscount)
    {
        $this->upSellDiscount = $upSellDiscount;

        return $this;
    }

    /**
     * Get upSellDiscount
     *
     * @return float
     */
    public function getUpSellDiscount()
    {
        return (float)$this->upSellDiscount;
    }

    /**
     * @return float
     */
    public function getUpSellAndShareDiscount()
    {
        return $this->getUpSellDiscount() + $this->getDiscount();
    }

    /**
     * @return float
     */
    public function getAllDiscounts()
    {
        return $this->loyalityDiscount + $this->upSellDiscount;
    }

    /**
     * @return float
     */
    public function getDiscountsPtc()
    {
        if ($this->getDiscountedTotalPrice()){
            return (int)(($this->getLoyalityDiscount() / $this->getDiscountedTotalPrice()) * 100);
        }

        return 0;
    }

    /**
     * @return string
     */
    public function getCustomTtn()
    {
        return $this->customTtn;
    }

    /**
     * @param $customTtn
     *
     * @return $this
     */
    public function setCustomTtn($customTtn)
    {
        $this->customTtn = $customTtn;

        return $this;
    }

    /**
     * @return bool
     */
    public function isWholesale()
    {
        return $this->getUsers() && $this->getUsers()->hasRole('ROLE_WHOLESALER');
    }

    /**
     * @return int
     */
    public function getBonusesWroteOff()
    {
        return $this->bonusesWroteOff;
    }

    /**
     * @param $bonusesWroteOff
     *
     * @return $this
     */
    public function setBonusesWroteOff($bonusesWroteOff)
    {
        $this->bonusesWroteOff = $bonusesWroteOff;

        return $this;
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->bonuses > $this->getMaxBonuses()) {
            $context->buildViolation('Bonuses count bigger then allowed')
                ->atPath('bonuses')
                ->addViolation();
        }
    }
}
