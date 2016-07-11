<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank as Assert;

/**
 * Users
 */
class Users extends BaseUser
{
    const OAUTH_VKONTAKTE = 0;

    const OAUTH_FACEBOOK = 1;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    private $uid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $surname;

    /**
     * @var string
     */
    private $middlename;

    /**
     * @var string
     * @Assert/NotBlank()
     */
    protected $phone;

    /**
     * @var string
     */
    protected $counterpartyRef;

    /**
     * @var integer
     */
    protected $bonuses = 0;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var string
     */
    protected $facebook_id;

    /**
     * @var string
     */
    protected $facebook_access_token;
    /**
     * @var string
     */
    protected $vkontakte_id;

    /**
     * @var string
     */
    protected $vkontakte_access_token;

    /**
     * @var boolean
     */
    protected $grayListFlag = false;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $orders;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set uid
     *
     * @param integer $uid
     *
     * @return Users
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Users
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
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return Users
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
     * @return Users
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
     * Set facebookId
     *
     * @param string $facebookId
     *
     * @return Users
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set facebookAccessToken
     *
     * @param string $facebookAccessToken
     *
     * @return Users
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebookAccessToken
     *
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Users
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
     * Set surname
     *
     * @param string $surname
     *
     * @return Users
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set middlename
     *
     * @param string $middlename
     *
     * @return Users
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * Get middlename
     *
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * Set counterpartyRef
     *
     * @param string $counterpartyRef
     *
     * @return Users
     */
    public function setCounterpartyRef($counterpartyRef)
    {
        $this->counterpartyRef = $counterpartyRef;

        return $this;
    }

    /**
     * Get counterpartyRef
     *
     * @return string
     */
    public function getCounterpartyRef()
    {
        return $this->counterpartyRef;
    }

    /**
     * Set vkontakteId
     *
     * @param string $vkontakteId
     *
     * @return Users
     */
    public function setVkontakteId($vkontakteId)
    {
        $this->vkontakte_id = $vkontakteId;

        return $this;
    }

    /**
     * Get vkontakteId
     *
     * @return string
     */
    public function getVkontakteId()
    {
        return $this->vkontakte_id;
    }

    /**
     * Set vkontakteAccessToken
     *
     * @param string $vkontakteAccessToken
     *
     * @return Users
     */
    public function setVkontakteAccessToken($vkontakteAccessToken)
    {
        $this->vkontakte_access_token = $vkontakteAccessToken;

        return $this;
    }

    /**
     * Get vkontakteAccessToken
     *
     * @return string
     */
    public function getVkontakteAccessToken()
    {
        return $this->vkontakte_access_token;
    }

    /**
     * Set bonuses
     *
     * @param integer $bonuses
     *
     * @return Users
     */
    public function setBonuses($bonuses)
    {
        $this->bonuses = $bonuses;

        return $this;
    }

    /**
     * Increment bonuses
     *
     * @param integer $bonuses
     *
     * @return Users
     */
    public function incrementBonuses($bonuses)
    {
        $this->bonuses += $bonuses;

        return $this;
    }

    /**
     * Decrement bonuses
     *
     * @param integer $bonuses
     *
     * @return Users
     */
    public function decrementBonuses($bonuses)
    {
        $this->bonuses -= $bonuses;

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
     * Set grayListFlag
     *
     * @param boolean $grayListFlag
     *
     * @return Users
     */
    public function setGrayListFlag($grayListFlag)
    {
        $this->grayListFlag = $grayListFlag;

        return $this;
    }

    /**
     * Get grayListFlag
     *
     * @return boolean
     */
    public function getGrayListFlag()
    {
        return $this->grayListFlag;
    }

    /**
     * Add order
     *
     * @param \AppBundle\Entity\Orders $order
     *
     * @return Users
     */
    public function addOrder(\AppBundle\Entity\Orders $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \AppBundle\Entity\Orders $order
     */
    public function removeOrder(\AppBundle\Entity\Orders $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Get oauth provider
     *
     * @return integer|null
     */
    public function getOauthProvider()
    {
        if($this->facebook_id) {
            return self::OAUTH_FACEBOOK;
        } elseif($this->vkontakte_id) {
            return self::OAUTH_VKONTAKTE;
        }
        return null;
    }
}
