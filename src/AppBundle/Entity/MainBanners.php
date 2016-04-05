<?php

namespace AppBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * MainBanners
 */
class MainBanners
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $titleButton;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $picture;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var boolean
     */
    private $active;

    /**
     * @var boolean
     */
    private $wide;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $updateTime;

    /**
     * Unmapped property to handle file uploads
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
     * Set titleButton
     *
     * @param string $titleButton
     *
     * @return MainBanners
     */
    public function setTitleButton($titleButton)
    {
        $this->titleButton = $titleButton;

        return $this;
    }

    /**
     * Get titleButton
     *
     * @return string
     */
    public function getTitleButton()
    {
        return $this->titleButton;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return MainBanners
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return MainBanners
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return MainBanners
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return MainBanners
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
     * Set active
     *
     * @param boolean $wide
     *
     * @return MainBanners
     */
    public function setWide($wide)
    {
        $this->wide = $wide;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getWide()
    {
        return $this->wide;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return MainBanners
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
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return MainBanners
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
     * @return MainBanners
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
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // we use the original file name here but you should
        // sanitize it at least to avoid any security issues


        $path = $this->fileUniqueName() . '.' . $this->getFile()->guessExtension();
        // move takes the target directory and target filename as params
        $this->getFile()->move(
            realpath($this->getUploadRootDir()),
            $path
        );

        // set the path property to the filename where you've saved the file
        $this->setPicture($path);

        // clean up the file property as you won't need it anymore
        $this->setFile(null);
    }

    private function fileUniqueName()
    {
        return sha1(uniqid(mt_rand(), true));
    }

    /**
     * getUploadRootDir
     * the absolute directory path where uploaded
     * documents should be saved
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    /**
     * getUploadDir
     * get rid of the __DIR__ so it doesn't screw up
     * when displaying uploaded /img/products in the view.
     *
     * @return string
     * /img/products
     */
    protected function getUploadDir()
    {
        return '/img/banners/';
    }

    public function getWebPath(){
        return $this->getUploadDir().$this->getPicture();
    }

    /**
     * Lifecycle callback to upload the file to the server
     */
    public function lifecycleFileUpload()
    {
        $this->upload();
    }

    public function __toString() {
        return $this->getTitle();
    }
}
