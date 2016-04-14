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
     * @param Request $request
     * @return Response
     */
    public function uploadFileAction(Request $request)
    {

        $filename = $_FILES['file'];
        $uploadPath = $this->get('uploader')->upload($this->container->getParameter('upload_img_directory'), $filename);
        $productId = (int)$request->request->get('model');
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Products')->findOneById($productId);
        $lastImage = $product->getImages()->last();

        if($lastImage) {
            $priority = $lastImage->getPriority() + 1;
        } else {
            $priority = 1;
        }

        $producImage = new \AppBundle\Entity\ProductImages;
        $producImage
            ->setLink($uploadPath)
            ->setPriority($priority)
            ->setProduct($product);
        
        $em->persist($producImage);
        
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
                        'filePath' => $uploadPath, # полный путь к нему
                        'priority' => $priority
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
        $producImagesIds = $this->getRequest()->request->get('imageIds');
        $ids = json_decode($producImagesIds);
        $images = $this->get('sort')
            ->rebuildPriorityProductModelImages($ids);

        return new Response(json_encode(array(
                    'status' => 1,
                )
            )
        );
    }

}
