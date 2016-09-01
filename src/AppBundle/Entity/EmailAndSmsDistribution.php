<?php

namespace AppBundle\Entity;

/**
 * EmailAndSmsDistribution
 */
class EmailAndSmsDistribution
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
     * @var boolean
     */
    private $sendSms = false;

    /**
     * @var string
     */
    private $smsText;

    /**
     * @var boolean
     */
    private $sendEmail = false;

    /**
     * @var string
     */
    private $emailTitle;

    /**
     * @var string
     */
    private $emailText;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var \AppBundle\Entity\DistributionSmsInfo
     */
    private $smsInfos;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return EmailAndSmsDistribution
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
     * Set sendSms
     *
     * @param boolean $sendSms
     *
     * @return EmailAndSmsDistribution
     */
    public function setSendSms($sendSms)
    {
        $this->sendSms = $sendSms;

        return $this;
    }

    /**
     * Get sendSms
     *
     * @return boolean
     */
    public function getSendSms()
    {
        return $this->sendSms;
    }

    /**
     * Set smsText
     *
     * @param string $smsText
     *
     * @return EmailAndSmsDistribution
     */
    public function setSmsText($smsText)
    {
        $this->smsText = $smsText;

        return $this;
    }

    /**
     * Get smsText
     *
     * @return string
     */
    public function getSmsText()
    {
        return $this->smsText;
    }

    /**
     * Set sendEmail
     *
     * @param boolean $sendEmail
     *
     * @return EmailAndSmsDistribution
     */
    public function setSendEmail($sendEmail)
    {
        $this->sendEmail = $sendEmail;

        return $this;
    }

    /**
     * Get sendEmail
     *
     * @return boolean
     */
    public function getSendEmail()
    {
        return $this->sendEmail;
    }

    /**
     * Set emailTitle
     *
     * @param string $emailTitle
     *
     * @return EmailAndSmsDistribution
     */
    public function setEmailTitle($emailTitle)
    {
        $this->emailTitle = $emailTitle;

        return $this;
    }

    /**
     * Get emailTitle
     *
     * @return string
     */
    public function getEmailTitle()
    {
        return $this->emailTitle;
    }

    /**
     * Set emailText
     *
     * @param string $emailText
     *
     * @return EmailAndSmsDistribution
     */
    public function setEmailText($emailText)
    {
        $this->emailText = $emailText;

        return $this;
    }

    /**
     * Get emailText
     *
     * @return string
     */
    public function getEmailText()
    {
        return $this->emailText;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return EmailAndSmsDistribution
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
     * @return EmailAndSmsDistribution
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
     * Set smsInfos
     *
     * @param \AppBundle\Entity\DistributionSmsInfo $smsInfos
     *
     * @return EmailAndSmsDistribution
     */
    public function setSmsInfos(\AppBundle\Entity\DistributionSmsInfo $smsInfos = null)
    {
        $this->smsInfos = $smsInfos;

        return $this;
    }

    /**
     * Get smsInfos
     *
     * @return \AppBundle\Entity\DistributionSmsInfo
     */
    public function getSmsInfos()
    {
        return $this->smsInfos;
    }

    /**
     * Add user
     *
     * @param \AppBundle\Entity\Users $user
     *
     * @return EmailAndSmsDistribution
     */
    public function addUser(\AppBundle\Entity\Users $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\Users $user
     */
    public function removeUser(\AppBundle\Entity\Users $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}

