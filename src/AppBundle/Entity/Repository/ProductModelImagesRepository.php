<?php

namespace AppBundle\Entity\Repository;

/**
 * Class: ProductModelImagesRepository
 *
 * @see \Doctrine\ORM\EntityRepository
 */
class ProductModelImagesRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * findOneByLinkForModel
     *
     * @param mixed $img
     * @param mixed $modelId
     *
     * @return mixed
     */
    public function findOneByLinkForModel($img, $modelId)
    {
        return $this->createQueryBuilder('images')
            ->select('images')
            ->innerJoin('images.productModels', 'models')->addSelect('models')
            ->andwhere('images.link = :img')->setParameter('img', $img)
            ->andwhere('models.id = :modelId')->setParameter('modelId', $modelId)
            ->getQuery()->getSingleResult();
    }
    
}
