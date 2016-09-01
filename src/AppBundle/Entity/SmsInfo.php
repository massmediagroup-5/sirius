<?php

namespace AppBundle\Entity;

/**
 * SmsInfo
 */
class SmsInfo
{
    /**
     * @var string
     */
    private $smsId;

    /**
     * @var string
     */
    private $smsStatus;


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

