<?php

namespace AppBundle\Entity;

/**
 * SiteParams
 */
class SiteParams
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $paramName;

    /**
     * @var string
     */
    private $paramValue;


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
     * Set paramName
     *
     * @param string $paramName
     *
     * @return SiteParams
     */
    public function setParamName($paramName)
    {
        $this->paramName = $paramName;

        return $this;
    }

    /**
     * Get paramName
     *
     * @return string
     */
    public function getParamName()
    {
        return $this->paramName;
    }

    /**
     * Set paramValue
     *
     * @param string $paramValue
     *
     * @return SiteParams
     */
    public function setParamValue($paramValue)
    {
        $this->paramValue = $paramValue;

        return $this;
    }

    /**
     * Get paramValue
     *
     * @return string
     */
    public function getParamValue()
    {
        return $this->paramValue;
    }
}
