<?php

namespace AppAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class: UploadController
 *
 * @see Controller
 */
class UploadController extends Controller
{

    /**
     * uploadFileAction
     *
     * @return Response
     */
    public function uploadFileAction()
    {
 
        $filename = $_FILES['file']; # принимает наш файл
        $uploadPath = $this->upload($filename); # запускаем функцию загрузки
        $modelId = (int)$this->getRequest()->request->get('model');

        $em = $this->getDoctrine()->getManager();
        $productModels = $em
            ->getRepository('AppBundle:ProductModels')
            ->findOneById($modelId);
        $productModelImage = new \AppBundle\Entity\ProductModelImages;
        $productModelImage
            ->setThumbnail($uploadPath)
            ->setProductModels($productModels)
            ;
        $em->persist($productModelImage);
        $em->flush();
 
        /**
          * Тут думаю ясно. Обычный ответ на запрос
          */
        return null === $uploadPath
            ? new Response(json_encode(array(
                        'status' => 0,
                        'message' => 'Wrong file type'
                    )
                )
            )
            : new Response(json_encode(array(
                        'status' => 1,
                        'message' => $filename, # имя файла
                        'filePath' => $uploadPath # полный путь к нему
                    )
                )
            );
    }

    /**
     * sortProductModelsImagesAction
     *
     * @param Request $request
     *
     * @return Response
     */
    public function sortProductModelsImagesAction(Request $request)
    {
        $productModelImagesIds = $this->getRequest()->request->get('imageIds');
        $ids = json_decode($productModelImagesIds);
        $images = $this->get('sort')
            ->rebuildPriorityProductModelImages($ids);

        return new Response(json_encode(array(
                    'status' => 1,
                )
            )
        );
    }
    
 
    private function getFoldersForUploadFile($type)
    {
        $fileType = $this->returnExistFileType($type); #метод возвращающюй тип файлов которые можно грузить
 
        if ($fileType !== null) {
            return array(
                'root_dir' => $this->container->getParameter('upload_' . $fileType . '_root_directory'), # полный путь к папке с картинкой
                'dir' => $this->container->getParameter('upload_' . $fileType . '_directory'), # отосительный путь к папке
            );
        } else {
            return null;
        }
    }
 
    # метод возвращает ключ(тип) файла который будет закачиваться
    private function returnExistFileType($type)
    {
        $typeArray = array(
            'img' => array(
                'image/png',
                'image/jpg',
                'image/jpeg',
            )
        );
 
        foreach ($typeArray as $key => $value) {
            if (in_array($type, $value)) {
                return $key;
            }
        }
 
        return null;
    }
 
    # Тут собственно все и происходит. Загрузка, присвоение имени, перемещение в папку
    private function upload($file)
    {
        $filePath = $this->getFoldersForUploadFile($file['type']);
 
        if (null === $this->getFileInfo($file['name']) || $filePath === null) {
 
            return null;
        }
        $pathInfo = $this->getFileInfo($file['name']);
        $path = $this->fileUniqueName() . '.' . $pathInfo['extension'];
        $this->uploadFileToFolder($file['tmp_name'], $path, $filePath['root_dir']);
 
        unset($file);
        //return $filePath['dir'] . DIRECTORY_SEPARATOR . $path;
        return $path;
    }
 
    # возвращает всю информацию о загруженном фале (что бы это не было)
    private function getFileInfo($file)
    {
 
        return $file !== null ? (array)pathinfo($file) : null;
    }
 
    # формирует уникальное имя
    private function fileUniqueName()
    {
 
        return sha1(uniqid(mt_rand(), true));
    }
    
    # перемещает файл в необходимую папку
    private function uploadFileToFolder($tmpFile, $newFileName, $rootFolder)
    {
        $e = new File($tmpFile);
        $e->move($rootFolder, $newFileName);
    }

}
