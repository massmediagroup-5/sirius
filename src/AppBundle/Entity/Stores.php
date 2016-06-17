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
     * @var string
     */
    private $ref;

    /**
     * @var string
     */
    private $fullJson;

    /**
     * @var boolean
     */
    private $active;

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
     * Set ref
     *
     * @param string $ref
     *
     * @return Stores
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref
     *
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set fullJson
     *
     * @param string $fullJson
     *
     * @return Stores
     */
    public function setFullJson($fullJson)
    {
        $this->fullJson = $fullJson;

        return $this;
    }

    /**
     * Get fullJson
     *
     * @return string
     */
    public function getFullJson()
    {
        return $this->fullJson;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Stores
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }
}
