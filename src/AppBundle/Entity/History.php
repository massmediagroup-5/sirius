<?php

namespace AppBundle\Entity;

use Illuminate\Support\Arr;

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
    private $changeType;
    
    /**
     * @var string
     */
    private $changed;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $additional;

    /**
     * @var \AppBundle\Entity\Users
     */
    private $user;


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
     * Set changed
     *
     * @param string $changed
     *
     * @return History
     */
    public function setChanged($changed)
    {
        $this->changed = $changed;

        return $this;
    }

    /**
     * Get changed
     *
     * @return string
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * Set from
     *
     * @param string $from
     *
     * @return History
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param string $to
     *
     * @return History
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\Users $user
     *
     * @return History
     */
    public function setUser(\AppBundle\Entity\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\Users
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set changeType
     *
     * @param string $changeType
     *
     * @return History
     */
    public function setChangeType($changeType)
    {
        $this->changeType = $changeType;

        return $this;
    }

    /**
     * Get changeType
     *
     * @return string
     */
    public function getChangeType()
    {
        return $this->changeType;
    }

    /**
     * Set additional
     *
     * @param string $additional
     *
     * @return History
     */
    public function setAdditional($additional)
    {
        $this->additional = serialize($additional);

        return $this;
    }

    /**
     * Get additional
     *
     * @return string
     */
    public function getAdditional()
    {
        $args = func_get_args();
        $additional = unserialize($this->additional);
        return isset($args[0]) ? Arr::get($additional, $args[0]) : $additional;
    }
}
