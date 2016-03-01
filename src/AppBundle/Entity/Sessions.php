<?php

namespace AppBundle\Entity;

/**
 * Sessions
 */
class Sessions
{
    /**
     * @var binary
     */
    private $sessId;

    /**
     * @var string
     */
    private $sessData;

    /**
     * @var integer
     */
    private $sessTime;

    /**
     * @var integer
     */
    private $sessLifetime;


    /**
     * Get sessId
     *
     * @return binary
     */
    public function getSessId()
    {
        return $this->sessId;
    }

    /**
     * Set sessData
     *
     * @param string $sessData
     *
     * @return Sessions
     */
    public function setSessData($sessData)
    {
        $this->sessData = $sessData;

        return $this;
    }

    /**
     * Get sessData
     *
     * @return string
     */
    public function getSessData()
    {
        return $this->sessData;
    }

    /**
     * Set sessTime
     *
     * @param integer $sessTime
     *
     * @return Sessions
     */
    public function setSessTime($sessTime)
    {
        $this->sessTime = $sessTime;

        return $this;
    }

    /**
     * Get sessTime
     *
     * @return integer
     */
    public function getSessTime()
    {
        return $this->sessTime;
    }

    /**
     * Set sessLifetime
     *
     * @param integer $sessLifetime
     *
     * @return Sessions
     */
    public function setSessLifetime($sessLifetime)
    {
        $this->sessLifetime = $sessLifetime;

        return $this;
    }

    /**
     * Get sessLifetime
     *
     * @return integer
     */
    public function getSessLifetime()
    {
        return $this->sessLifetime;
    }
}
