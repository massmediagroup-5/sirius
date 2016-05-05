<?php

namespace AppBundle\Entity;

/**
 * History
 */
class History
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var string
     */
    private $text;

    /**
     * @var \AppBundle\Entity\Orders
     */
    private $order;


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
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return History
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime ? : new \DateTime();
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return History
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set order
     *
     * @param \AppBundle\Entity\Orders $order
     *
     * @return History
     */
    public function setOrder(\AppBundle\Entity\Orders $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \AppBundle\Entity\Orders
     */
    public function getOrder()
    {
        return $this->order;
    }
}
