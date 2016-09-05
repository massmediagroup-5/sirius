<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;


class DistributionSmsInfo extends SmsInfo
{

    /**
     * @var \AppBundle\Entity\EmailAndSmsDistribution
     */
    private $distribution;

    /**
     * @var \AppBundle\Entity\Users
     */
    private $users;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * Set distribution
     *
     * @param \AppBundle\Entity\EmailAndSmsDistribution $distribution
     *
     * @return DistributionSmsInfo
     */
    public function setDistribution(\AppBundle\Entity\EmailAndSmsDistribution $distribution = null)
    {
        $this->distribution = $distribution;

        return $this;
    }

    /**
     * Get distribution
     *
     * @return \AppBundle\Entity\EmailAndSmsDistribution
     */
    public function getDistribution()
    {
        return $this->distribution;
    }


    /**
     * Set users
     *
     * @param \AppBundle\Entity\Users $users
     *
     * @return DistributionSmsInfo
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
}
