<?php

namespace AppBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Upload file, here only uploading logic
 *
 * Class: Upload
 * @author zimm
 */
class Upload
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * __construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Upload file to web directory, return path to file related to web directory
     *
     * @param mixed $path
     * @param mixed $file
     * @return string|null $file
     */
    public function upload($path, $file)
    {
        if($file instanceof UploadedFile) {
            $extension = $file->getClientOriginalExtension();
        } else {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $file = new File($file['tmp_name']);
        }

        $fileName = $this->fileUniqueName() . '.' . $extension;
        $subFolder = $this->generateFolderName();
        $folderPath = "{$this->container->getParameter('web_dir')}/$path/$subFolder";
        $relPathName = "/$path/$subFolder/$fileName";

        $file->move($folderPath, $fileName);

        return $relPathName;
    }

    /**
     * Generate unique file name
     * @return string
     */
    private function fileUniqueName()
    {

        return sha1(uniqid(mt_rand(), true));
    }

    /**
     * Generate folder name and unique file name
     * @return string
     */
    private function generateFolderName()
    {
        return mb_strcut(sha1(uniqid(mt_rand(), true)), 0, 2);
    }

}
