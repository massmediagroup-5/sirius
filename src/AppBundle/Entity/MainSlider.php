<?php

namespace AppBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * MainSlider
 */
class MainSlider
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
    private $alias;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $picture;

    /**
     * @var boolean
     */
    private $active;

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
     * Set title
     *
     * @param string $title
     *
     * @return MainSlider
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
     * @return MainSlider
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
     * Set description
     *
     * @param string $description
     *
     * @return MainSlider
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return MainSlider
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
     * Set active
     *
     * @param boolean $active
     *
     * @return MainSlider
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
     * @return MainSlider
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
     * @return MainSlider
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

    # ��������� ���������� ���
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
        return '/img/slider/';
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
}
