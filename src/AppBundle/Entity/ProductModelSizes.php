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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sizes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sizes = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    /**
     * Add size
     *
     * @param \AppBundle\Entity\ProductModelSpecificSize $size
     *
     * @return ProductModelSizes
     */
    public function addSize(\AppBundle\Entity\ProductModelSpecificSize $size)
    {
        $this->sizes[] = $size;

        return $this;
    }

    /**
     * Remove size
     *
     * @param \AppBundle\Entity\ProductModelSpecificSize $size
     */
    public function removeSize(\AppBundle\Entity\ProductModelSpecificSize $size)
    {
        $this->sizes->removeElement($size);
    }

    /**
     * Get sizes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    public function __toString()
    {
        return $this->getSize() ? : '';
    }
}
