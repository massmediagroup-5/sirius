<?php

namespace AppBundle\Entity;

/**
 * ReturnedSizesHistory
 */
class ReturnedSizesHistory extends History
{
    /**
     * @var \stdClass
     */
    private $returnedSizes;


    /**
     * Set returnedSizes
     *
     * @param \AppBundle\Entity\ReturnedSizes $returnedSizes
     *
     * @return ReturnedSizesHistory
     */
    public function setReturnedSizes($returnedSizes = null)
    {
        $this->returnedSizes = $returnedSizes;

        return $this;
    }

    /**
     * Get returnedSizes
     *
     * @return \AppBundle\Entity\ReturnedSizes
     */
    public function getReturnedSizes()
    {
        return $this->returnedSizes;
    }
}

