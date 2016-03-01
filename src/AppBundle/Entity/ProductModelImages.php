<?php

namespace AppBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * ProductModelImages
 */
class ProductModelImages
{

    /**
     * file
     *
     * @var mixed
     */
    private $file;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $link;

    /**
     * @var string
     */
    private $thumbnail;

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
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
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
     * Set thumbnail
     *
     * @param string $thumbnail
     *
     * @return ProductModelImages
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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
    
    /**
     * lifecycleFileUpload
     * Lifecycle callback to upload the file to the server
     *
     * @param mixed $obj
     */
    public function lifecycleFileUpload($obj)
    {
        $this->upload($obj);
    }

    /**
     * Get thumbnail
     *
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return ProductModelImages
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * getAbsolutePath
     *
     */
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    /**
     * getWebPath
     *
     */
    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    /**
     * upload
     * Manages the copying of the file to the relevant place
     * on the server
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $obj
     */
    private function upload(\Doctrine\ORM\Event\LifecycleEventArgs $obj)
    {
        // Get EntityManager
        $em = $obj->getObjectManager();

        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }
    
        // Make new filename.
        $extention = $this->getFile()->guessExtension();
        $thumbnail = sha1(uniqid(mt_rand(), true)) . '.' . $extention;

        // move takes the target directory and target filename as params
        $this->getFile()->move(
            realpath($this->getUploadRootDir()),
            $thumbnail
        );
    
        // find parrent ProductModel Id.
        $productModels = $em->getunitOfWork()
            ->getIdentityMap()['AppBundle\Entity\ProductModels'];
        $productModelsId = array_shift($productModels)->getId();

        // set ProdutcModels
        $this->productModels = $em
            ->getRepository('AppBundle:ProductModels')
            ->find($productModelsId);
        // set the thumbnail of the file
        $this->thumbnail = $thumbnail;

        // clean up the file property as you won't need it anymore
        $this->setFile(null);
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
        return '/img/products/';
    }
}
