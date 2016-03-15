<?php

namespace AppAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        $filename = $_FILES['file'];
        $uploadPath = $this->get('uploader')->upload($this->container->getParameter('upload_img_directory'), $filename);
        $modelId = (int)$this->getRequest()->request->get('model');

        $em = $this->getDoctrine()->getManager();
        $productModels = $em
            ->getRepository('AppBundle:ProductModels')
            ->findOneById($modelId);
        $productModelImage = new \AppBundle\Entity\ProductModelImages;
        $productModelImage
            ->setLink($uploadPath)
            ->setProductModels($productModels);
        $em->persist($productModelImage);
        $em->flush();

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

}
