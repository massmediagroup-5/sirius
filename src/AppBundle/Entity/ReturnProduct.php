<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
/**
 * ReturnProduct
 */
class ReturnProduct
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @Assert\NotBlank
     */
    private $order;

    /**
     * @var Users $user
     */
    private $user;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $returnedSizes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $history;

    /**
     * @var string
     */
    private $returnDescription;

    /**
     * @var string
     */
    private $status;

    /**
     * created Time/Date
     *
     * @var \DateTime
     *
     */
    protected $createdAt;

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
     * @return Orders
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Orders $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return Users
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Users $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Set createdAt
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getReturnDescription()
    {
        return $this->returnDescription;
    }

    /**
     * @param string $returnDescription
     */
    public function setReturnDescription($returnDescription)
    {
        $this->returnDescription = $returnDescription;
    }

    /**
     * @return mixed
     */
    public function getReturnedSizes()
    {
        return $this->returnedSizes;
    }

    /**
     * @param $returnedSizes
     * @return $this
     */
    public function addReturnedSizes($returnedSizes)
    {
        $this->returnedSizes[] = $returnedSizes;

        return $this;
    }

    /**
     * @param mixed $returnedSizes
     */
    public function setReturnedSizes($returnedSizes)
    {
        $this->returnedSizes = $returnedSizes;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->returnedSizes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get history
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Add history
     *
     * @param ReturnProductHistory $history
     *
     * @return ReturnProduct
     */
    public function addHistory(ReturnProductHistory $history)
    {
        $this->history[] = $history;

        return $this;
    }

    /**
     * Add returnedSize
     *
     * @param \AppBundle\Entity\ReturnedSizes $returnedSize
     *
     * @return ReturnProduct
     */
    public function addReturnedSize(\AppBundle\Entity\ReturnedSizes $returnedSize)
    {
        $this->returnedSizes[] = $returnedSize;

        return $this;
    }

    /**
     * Remove returnedSize
     *
     * @param \AppBundle\Entity\ReturnedSizes $returnedSize
     */
    public function removeReturnedSize(\AppBundle\Entity\ReturnedSizes $returnedSize)
    {
        $this->returnedSizes->removeElement($returnedSize);
    }

    /**
     * Remove history
     *
     * @param \AppBundle\Entity\ReturnProductHistory $history
     */
    public function removeHistory(\AppBundle\Entity\ReturnProductHistory $history)
    {
        $this->history->removeElement($history);
    }
}
