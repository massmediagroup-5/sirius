<?php

namespace AppBundle\Entity;

/**
 * SocialNetworks
 */
class SocialNetworks
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $link;

    /**
     * @var string
     */
    private $picture;

    /**
     * @var boolean
     */
    private $active;


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
     * Set name
     *
     * @param string $name
     *
     * @return SocialNetworks
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return SocialNetworks
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
     * Set active
     *
     * @param boolean $active
     *
     * @return SocialNetworks
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return SocialNetworks
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return boolean
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * getUploadRootDir
     * the absolute directory path where uploaded
     * documents should be saved
     *
     * @return string
     */
    public function getUploadRootDir()
    {
        // dump(realpath(__DIR__.'/../../../web/'.$this->getUploadDir()));exit;
        // absolute path to your directory where images must be saved
        $path = __DIR__ . '/../../../web/' . $this->getUploadDir();
        $path = str_replace('\\', '/', $path);

        return $path;
    }

    /**
     * getUploadDir
     * get rid of the __DIR__ so it doesn't screw up
     * when displaying uploaded /img/slider in the view.
     *
     * @return string
     * /img/products
     */
    public function getUploadDir()
    {
        return 'img/social';
    }

    public function getAbsolutePath()
    {
        return null === $this->getPicture() ? null : $this->getUploadRootDir() . '/' . $this->getPicture();
    }

    public function getWebPath()
    {
        return null === $this->getPicture() ? null : '/' . $this->getUploadDir() . '/' . $this->getPicture();
    }

    public function __toString()
    {
        return $this->getName();
    }
}
