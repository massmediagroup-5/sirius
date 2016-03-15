<?php

namespace AppBundle\Entity;

/**
 * PageImages
 */
class PageImages
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $link;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var \AppBundle\Entity\Pages
     */
    private $pages;


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
     * Set link
     *
     * @param string $link
     *
     * @return PageImages
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return PageImages
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
        return $this->createTime;
    }

    /**
     * Set updateTime
     *
     * @param \DateTime $updateTime
     *
     * @return PageImages
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;

        return $this;
    }

    /**
     * Get updateTime
     *
     * @return \DateTime
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * Set pages
     *
     * @param \AppBundle\Entity\Pages $pages
     *
     * @return PageImages
     */
    public function setPages(\AppBundle\Entity\Pages $pages = null)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Get pages
     *
     * @return \AppBundle\Entity\Pages
     */
    public function getPages()
    {
        return $this->pages;
    }
}
