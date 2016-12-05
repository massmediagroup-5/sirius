<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ReturnedSizes
 */
class ReturnedSizes
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var OrderProductSize
     */
    private $size;

    /**
     * @var integer
     */
    private $count;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $history;

    /**
     * @return ReturnProduct $returnProduct
     */
    private $returnProduct;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->history = new ArrayCollection();
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
     * Set count
     *
     * @param integer $count
     *
     * @return ReturnedSizes
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReturnProduct()
    {
        return $this->returnProduct;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $returnProduct
     */
    public function setReturnProduct($returnProduct)
    {
        $this->returnProduct = $returnProduct;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    public function addHistory(History $history)
    {
        $this->history[] = $history;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Remove history
     *
     * @param \AppBundle\Entity\ReturnedSizesHistory $history
     */
    public function removeHistory(\AppBundle\Entity\ReturnedSizesHistory $history)
    {
        $this->history->removeElement($history);
    }
}
