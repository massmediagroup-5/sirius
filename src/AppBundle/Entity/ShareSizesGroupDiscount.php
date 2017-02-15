<?php

namespace AppBundle\Entity;

/**
 * ShareSizesGroupDiscount
 */
class ShareSizesGroupDiscount
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $discount;

    /**
     * @var \AppBundle\Entity\ShareSizesGroup
     */
    private $shareGroup;

    /**
     * @var \AppBundle\Entity\ShareSizesGroup
     */
    private $companion;


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
     * Set discount
     *
     * @param integer $discount
     *
     * @return ShareSizesGroupDiscount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return integer
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set shareGroup
     *
     * @param \AppBundle\Entity\ShareSizesGroup $shareGroup
     *
     * @return ShareSizesGroupDiscount
     */
    public function setShareGroup(\AppBundle\Entity\ShareSizesGroup $shareGroup = null)
    {
        $this->shareGroup = $shareGroup;

        return $this;
    }

    /**
     * Get shareGroup
     *
     * @return \AppBundle\Entity\ShareSizesGroup
     */
    public function getShareGroup()
    {
        return $this->shareGroup;
    }

    /**
     * Set companion
     *
     * @param \AppBundle\Entity\ShareSizesGroup $companion
     *
     * @return ShareSizesGroupDiscount
     */
    public function setCompanion(\AppBundle\Entity\ShareSizesGroup $companion = null)
    {
        $this->companion = $companion;

        return $this;
    }

    /**
     * Get companion
     *
     * @return \AppBundle\Entity\ShareSizesGroup
     */
    public function getCompanion()
    {
        return $this->companion;
    }
}
