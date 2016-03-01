<?php

namespace AppBundle\Entity;

/**
 * ActionLabels
 */
class ActionLabels
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
    private $htmlClass;


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
     * @return ActionLabels
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
     * Set htmlClass
     *
     * @param string $htmlClass
     *
     * @return ActionLabels
     */
    public function setHtmlClass($htmlClass)
    {
        $this->htmlClass = $htmlClass;

        return $this;
    }

    /**
     * Get htmlClass
     *
     * @return string
     */
    public function getHtmlClass()
    {
        return $this->htmlClass;
    }
}
