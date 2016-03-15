<?php

namespace AppBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * ProductModelImages
 */
class ProductModelImages
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
     * @var integer
     */
    private $priority = '0';

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var \AppBundle\Entity\ProductModels
     */
    private $productModels;

    /**
     * @var
     */
    private $file;

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
     * @return ProductModelImages
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
     * Set priority
     *
     * @param integer $priority
     *
     * @return ProductModelImages
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return ProductModelImages
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
     * @return ProductModelImages
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
     * Set productModels
     *
     * @param \AppBundle\Entity\ProductModels $productModels
     *
     * @return ProductModelImages
     */
    public function setProductModels(\AppBundle\Entity\ProductModels $productModels = null)
    {
        $this->productModels = $productModels;

        return $this;
    }

    /**
     * Get productModels
     *
     * @return \AppBundle\Entity\ProductModels
     */
    public function getProductModels()
    {
        return $this->productModels;
    }

    public function setFile($file) {
        $this->file = $file;
    }

    public function getFile() {
        return $this->file;
    }

    public function __toString() {
        return $this->getLink() ?: '';
    }
}
