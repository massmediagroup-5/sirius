<?php

namespace AppBundle\Entity;

/**
 * SmsInfo
 */
abstract class SmsInfo
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $smsId;

    /**
     * @var string
     */
    private $smsStatus;


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
     * Set smsId
     *
     * @param string $smsId
     *
     * @return SmsInfo
     */
    public function setSmsId($smsId)
    {
        $this->smsId = $smsId;

        return $this;
    }

    /**
     * Get smsId
     *
     * @return string
     */
    public function getSmsId()
    {
        return $this->smsId;
    }

    /**
     * Set smsStatus
     *
     * @param string $smsStatus
     *
     * @return SmsInfo
     */
    public function setSmsStatus($smsStatus)
    {
        $this->smsStatus = $smsStatus;

        return $this;
    }

    /**
     * Get smsStatus
     *
     * @return string
     */
    public function getSmsStatus()
    {
        return $this->smsStatus;
    }
}
