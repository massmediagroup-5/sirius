<?php

namespace AppBundle\Entity\Repository;


use AppBundle\Entity\Orders;
use AppBundle\Entity\ProductModelSpecificSize;

/**
 * Class OrderProductSizeRepository
 * @package AppBundle\Entity\Repository
 */
class OrderProductSizeRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param ProductModelSpecificSize $size
     * @param Orders $order
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneBySizeAndOrder(ProductModelSpecificSize $size, Orders $order)
    {
        return $this->createQueryBuilder('orderSize')
            ->join('orderSize.size', 'size')
            ->where('size = :size AND orderSize.order = :order')
            ->setParameters(compact('size', 'order'))
            ->getQuery()
            ->getOneOrNullResult();
    }
}
