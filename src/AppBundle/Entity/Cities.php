<?php

namespace AppBundle\Entity;

/**
 * Cities
 */
class Cities
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
     * @var \AppBundle\Entity\Carriers
     */
    private $carriers;


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
     * @return Cities
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
     * Set carriers
     *
     * @param \AppBundle\Entity\Carriers $carriers
     *
     * @return Cities
     */
    public function setCarriers(\AppBundle\Entity\Carriers $carriers = null)
    {
        $this->carriers = $carriers;

        return $this;
    }

    /**
     * Get carriers
     *
     * @return \AppBundle\Entity\Carriers
     */
    public function getCarriers()
    {
        return $this->carriers;
    }
}
