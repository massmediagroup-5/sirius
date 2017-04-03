<?php

namespace AppBundle\Entity;


/**
 * Class DistributionEmailInfo
 *
 * @package AppBundle\Entity
 */
class DistributionEmailInfo
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var EmailAndSmsDistribution
     */
    private $distribution;

    /**
     * @var Users
     */
    private $user;

    /**
     * @var \DateTime
     */
    private $sentAt;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set distribution
     *
     * @param EmailAndSmsDistribution $distribution
     *
     * @return DistributionEmailInfo
     */
    public function setDistribution(EmailAndSmsDistribution $distribution = null)
    {
        $this->distribution = $distribution;

        return $this;
    }

    /**
     * Get distribution
     *
     * @return EmailAndSmsDistribution
     */
    public function getDistribution()
    {
        return $this->distribution;
    }


    /**
     * Set users
     *
     * @param Users $user
     *
     * @return DistributionEmailInfo
     */
    public function setUser(Users $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get users
     *
     * @return Users
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * @param $sentAt
     *
     * @return $this
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }
}
