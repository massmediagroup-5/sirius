<?php

namespace AppBundle\Entity;

/**
 * ProductModelSizes
 */
class ProductModelSizes
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $size;


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
     * Set size
     *
     * @param string $size
     *
     * @return ProductModelSizes
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }
}
