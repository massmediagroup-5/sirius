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

    /**
     * Set ref
     *
     * @param string $ref
     *
     * @return Cities
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
     * @return Cities
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
     * @return Cities
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
