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
     * @var boolean
     */
    private $active;

    /**
     * @var boolean
     */
    private $editor;


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

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return SiteParams
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
     * Set editor
     *
     * @param boolean $editor
     *
     * @return SiteParams
     */
    public function setEditor($editor)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * Get editor
     *
     * @return boolean
     */
    public function getEditor()
    {
        return $this->editor;
    }
}
