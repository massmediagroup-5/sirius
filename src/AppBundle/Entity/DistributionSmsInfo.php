<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;


class DistributionSmsInfo
{

    /**
     * @var \AppBundle\Entity\EmailAndSmsDistribution
     */
    private $distribution;


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
}
