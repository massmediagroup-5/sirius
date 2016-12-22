<?php

namespace AppBundle\Entity;

/**
 * Class AbstractLoyaltyProgram
 * @package AppBundle\Entity
 */
abstract class AbstractLoyaltyProgram
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $sumFrom;

    /**
     * @var string
     */
    private $sumTo;

    /**
     * @var string
     */
    private $discount;


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
     * Set sumFrom
     *
     * @param string $sumFrom
     *
     * @return AbstractLoyaltyProgram
     */
    public function setSumFrom($sumFrom)
    {
        $this->sumFrom = $sumFrom;

        return $this;
    }

    /**
     * Get sumFrom
     *
     * @return string
     */
    public function getSumFrom()
    {
        return $this->sumFrom;
    }

    /**
     * Set sumTo
     *
     * @param string $sumTo
     *
     * @return AbstractLoyaltyProgram
     */
    public function setSumTo($sumTo)
    {
        $this->sumTo = $sumTo;

        return $this;
    }

    /**
     * Get sumTo
     *
     * @return string
     */
    public function getSumTo()
    {
        return $this->sumTo;
    }

    /**
     * Set discount
     *
     * @param string $discount
     *
     * @return AbstractLoyaltyProgram
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }
}
