<?php

namespace AppBundle\Entity;

/**
 * OrderStatusPay
 */
class OrderStatusPay
{
    const CODE_PAID = 'paid';
    
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
    private $code;

    /**
     * @var string
     */
    private $description;

    /**
     * @var boolean
     */
    private $baseFlag = 0;

    /**
     * @var boolean
     */
    private $sendClient;

    /**
     * @var string
     */
    private $sendClientText;

    /**
     * @var string
     */
    private $sendClientNightText;

    /**
     * @var boolean
     */
    private $sendManager;

    /**
     * @var string
     */
    private $sendManagerText;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var boolean
     */
    private $active;

    /**
     * @var boolean
     */
    private $sendClientEmail;

    /**
     * @var string
     */
    private $sendClientEmailText;

    /**
     * @var boolean
     */
    private $sendManagerEmail;

    /**
     * @var string
     */
    private $sendManagerEmailText;


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
     * @return OrderStatusPay
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
     * Set code
     *
     * @param string $code
     *
     * @return OrderStatusPay
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return OrderStatusPay
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
     * Set baseFlag
     *
     * @param boolean $baseFlag
     *
     * @return OrderStatusPay
     */
    public function setBaseFlag($baseFlag)
    {
        $this->baseFlag = $baseFlag;

        return $this;
    }

    /**
     * Get baseFlag
     *
     * @return boolean
     */
    public function getBaseFlag()
    {
        return $this->baseFlag;
    }

    /**
     * Set sendClient
     *
     * @param boolean $sendClient
     *
     * @return OrderStatusPay
     */
    public function setSendClient($sendClient)
    {
        $this->sendClient = $sendClient;

        return $this;
    }

    /**
     * Get sendClient
     *
     * @return boolean
     */
    public function getSendClient()
    {
        return $this->sendClient;
    }

    /**
     * Set sendClientText
     *
     * @param string $sendClientText
     *
     * @return OrderStatusPay
     */
    public function setSendClientText($sendClientText)
    {
        $this->sendClientText = $sendClientText;

        return $this;
    }

    /**
     * Get sendClientText
     *
     * @return string
     */
    public function getSendClientText()
    {
        return $this->sendClientText;
    }

    /**
     * Set sendClientNightText
     *
     * @param string $sendClientNightText
     *
     * @return OrderStatusPay
     */
    public function setSendClientNightText($sendClientNightText)
    {
        $this->sendClientNightText = $sendClientNightText;

        return $this;
    }

    /**
     * Get sendClientNightText
     *
     * @return string
     */
    public function getSendClientNightText()
    {
        return $this->sendClientNightText;
    }

    /**
     * Set sendManager
     *
     * @param boolean $sendManager
     *
     * @return OrderStatusPay
     */
    public function setSendManager($sendManager)
    {
        $this->sendManager = $sendManager;

        return $this;
    }

    /**
     * Get sendManager
     *
     * @return boolean
     */
    public function getSendManager()
    {
        return $this->sendManager;
    }

    /**
     * Set sendManagerText
     *
     * @param string $sendManagerText
     *
     * @return OrderStatusPay
     */
    public function setSendManagerText($sendManagerText)
    {
        $this->sendManagerText = $sendManagerText;

        return $this;
    }

    /**
     * Get sendManagerText
     *
     * @return string
     */
    public function getSendManagerText()
    {
        return $this->sendManagerText;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return OrderStatusPay
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
     * Set active
     *
     * @param boolean $active
     *
     * @return OrderStatusPay
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set sendClientEmail
     *
     * @param boolean $sendClientEmail
     *
     * @return OrderStatusPay
     */
    public function setSendClientEmail($sendClientEmail)
    {
        $this->sendClientEmail = $sendClientEmail;

        return $this;
    }

    /**
     * Get sendClientEmail
     *
     * @return boolean
     */
    public function getSendClientEmail()
    {
        return $this->sendClientEmail;
    }

    /**
     * Set sendClientEmailText
     *
     * @param string $sendClientEmailText
     *
     * @return OrderStatusPay
     */
    public function setSendClientEmailText($sendClientEmailText)
    {
        $this->sendClientEmailText = $sendClientEmailText;

        return $this;
    }

    /**
     * Get sendClientEmailText
     *
     * @return string
     */
    public function getSendClientEmailText()
    {
        return $this->sendClientEmailText;
    }

    /**
     * @return bool
     */
    public function isSendManagerEmail()
    {
        return $this->sendManagerEmail;
    }

    /**
     * @param $sendManagerEmail
     *
     * @return $this
     */
    public function setSendManagerEmail($sendManagerEmail)
    {
        $this->sendManagerEmail = $sendManagerEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getSendManagerEmailText()
    {
        return $this->sendManagerEmailText;
    }

    /**
     * @param $sendManagerEmailText
     *
     * @return $this
     */
    public function setSendManagerEmailText($sendManagerEmailText)
    {
        $this->sendManagerEmailText = $sendManagerEmailText;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
