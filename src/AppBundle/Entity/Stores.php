<?php

namespace AppBundle\Entity;

/**
 * Stores
 */
class Stores
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
     * @var \AppBundle\Entity\Cities
     */
    private $cities;


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
     * @return Stores
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
     * Set cities
     *
     * @param \AppBundle\Entity\Cities $cities
     *
     * @return Stores
     */
    public function setCities(\AppBundle\Entity\Cities $cities = null)
    {
        $this->cities = $cities;

        return $this;
    }

    /**
     * Get cities
     *
     * @return \AppBundle\Entity\Cities
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }
}
