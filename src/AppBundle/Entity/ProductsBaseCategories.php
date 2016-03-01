<?php

namespace AppBundle\Entity;

/**
 * ProductsBaseCategories
 */
class ProductsBaseCategories
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
     * @var \DateTime
     */
    private $updateTime;

    /**
     * @var \AppBundle\Entity\Products
     */
    private $productsForBaseCategories;

    /**
     * @var \AppBundle\Entity\Categories
     */
    private $categories;


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
     * @return ProductsBaseCategories
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
     * @return ProductsBaseCategories
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
     * Set productsForBaseCategories
     *
     * @param \AppBundle\Entity\Products $productsForBaseCategories
     *
     * @return ProductsBaseCategories
     */
    public function setProductsForBaseCategories(\AppBundle\Entity\Products $productsForBaseCategories = null)
    {
        $this->productsForBaseCategories = $productsForBaseCategories;

        return $this;
    }

    /**
     * Get productsForBaseCategories
     *
     * @return \AppBundle\Entity\Products
     */
    public function getProductsForBaseCategories()
    {
        return $this->productsForBaseCategories;
    }

    /**
     * Set categories
     *
     * @param \AppBundle\Entity\Categories $categories
     *
     * @return ProductsBaseCategories
     */
    public function setCategories(\AppBundle\Entity\Categories $categories = null)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get categories
     *
     * @return \AppBundle\Entity\Categories
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
